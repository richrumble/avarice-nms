Option Explicit

' Searches the domain for computers, executes a child script under a number of seperate processes for each
' batch of computers. Passes computer list to child as a semi-colon delimited list.
'
' Uses Win32_Process to monitor threads.
'
' Change Log:
' * (18/02/2009) Added Thread Monitor using persistent dictionary initialised in ThreadManager, populated in GetProcessCount
' * (18/02/2009) Added hard-options (not command line options) to enable / disable Thead and Batch monitoring
' * (18/02/2009) Added batch number as command line parameter to child script execution (had been removed in earlier revision)
' * (18/02/2009) Changed LDAP query to retrieve dNSHostName instead of name. Support for forests containing child domains.
' * (19/02/2009) Prevented Number of Threads being 0
' * (12/05/2010) Added ChildArgument option, allowing additional arguments to be passed as a string to a child script

' Script Constants

Const ENABLE_THREAD_MONITOR = True
Const ENABLE_BATCH_PROGRESS_MONITOR = True
Const BATCH_DELIMITER = ","

Sub UsageText
  Dim strUsage : strUsage = "Usage:" & vbCrLf & vbCrLf
  strUsage = strUsage & WScript.ScriptName & " /ChildScript:<ScriptName> [/ChildArguments:<Arguments>]" & vbCrLf
  strUsage = strUsage & "        [/FinalCommand:<ScriptName>] [/NumComputers:<Integer>] [/NumThreads:<Integer>]" & vbCrLf
  strUsage = strUsage & "        [/Server:<Name>] [/BaseDN:<Distinguished Name>] [/LDAPFilter:<Filter String>]" & vbCrLf
  strUsage = strUsage & "        [/GlobalCatalog] [/OneLevel]" & vbCrLf & vbCrLf
  strUsage = strUsage & "Required arguments:" & vbCrLf & vbCrlf
  strUsage = strUsage & "    ChildScript       Script to execute as batch job" & vbCrLf & vbCrLf
  strUsage = strUsage & "Optional arguments: " & vbCrlf & vbCrLf
  strUsage = strUsage & "    ChildArguments    Arguments to pass to the child script" & vbCrLf
  strUsage = strUsage & "    FinalCommand      Command to execute after all processing is complete." & vbCrLf
  strUsage = strUsage & "    NumComputers      Maximum number of computer objects to assign to each batch. (Default 10)" & vbCrLf
  strUsage = strUsage & "    NumThreads        Maximum number of threads to execute simultaneously. (Default 10)" & vbCrLf
  strUsage = strUsage & "    Server            LDAP server for query. (Default logon server)" & vbCrLf
  strUsage = strUsage & "    BaseDN            Base distinguished name or OU used for search. (Default current domain)" & vbCrLf
  strUsage = strUsage & "    LDAPFilter        LDAP Filter used when performing search. (Default objectClass=computer)" & vbCrLf
  strUsage = strUsage & "    GlobalCatalog     Execute the search against a Global Catalog." & vbCrLf
  strUsage = strUsage & "    OneLevel          Execute the search for this level only (Default subtree)." & vbCrLf

  WScript.Echo strUsage
  WScript.Quit
End Sub

Function GetArgs
  Dim objArgs : Set objArgs = CreateObject("Scripting.Dictionary")

  Dim strChildScript : strChildScript = WScript.Arguments.Named("ChildScript")
  If strChildScript = "" Then
    UsageText
  End If
  Dim objFSO : Set objFSO = CreateObject("Scripting.FileSystemObject")
  If Not objFSO.FileExists(strChildScript) Then
    WScript.Echo "ERROR: Child script not found" & vbCrLf
    UsageText
  End If
  objArgs.Add "ChildScript", strChildScript
  Set objFSO = Nothing

  If WScript.Arguments.Named("ChildArguments") <> "" Then
    objArgs.Add "ChildArguments", WScript.Arguments.Named("ChildArguments")
  End If

  If WScript.Arguments.Named("FinalCommand") <> "" Then
    objArgs.Add "FinalCommand", WScript.Arguments.Named("FinalCommand")
  End If

  Dim strNumComputers : strNumComputers = WScript.Arguments.Named("NumComputers")
  If strNumComputers <> "" Then
    If IsNumeric(strNumComputers) Then
      objArgs.Add "NumComputers", CInt(strNumComputers)
    Else
      WScript.Echo "ERROR: NumComputers must be an integer value" & vbCrLf
    End If
  Else
    objArgs.Add "NumComputers", 10
  End If

  Dim strNumThreads : strNumThreads = WScript.Arguments.Named("NumThreads")
  If strNumThreads <> "" Then
    If IsNumeric(strNumThreads) Then
      objArgs.Add "NumThreads", CInt(strNumThreads)
    Else
      WScript.Echo "ERROR: NumThreads must be an integer value" & vbCrLf
    End If
  Else
    objArgs.Add "NumThreads", 10
  End If

  objArgs.Add "Server", ""
  If WScript.Arguments.Named("Server") <> "" Then
    objArgs("Server") = WScript.Arguments.Named("Server") & "/"
  End If

  If WScript.Arguments.Named("BaseDN") <> "" Then
    objArgs.Add "BaseDN", WScript.Arguments.Named("BaseDN")
  End If
  If Not objArgs.Exists("BaseDN") Then
    Dim objRootDSE : Set objRootDSE = GetObject("LDAP://" & objArgs("Server") & "RootDSE")
    objArgs.Add "BaseDN", objRootDSE.Get("defaultNamingContext")
    Set objRootDSE = Nothing
  End If

  objArgs.Add "LDAPFilter", "(objectClass=computer)"
  If WScript.Arguments.Named("LDAPFilter") <> "" Then
    objArgs("LDAPFilter") = WScript.Arguments.Named("LDAPFilter")
  End If

  objArgs.Add "Port", "LDAP"
  objArgs.Add "Scope", "subtree"

  Dim strArg
  For Each strArg in WScript.Arguments
    If LCase(strArg) = "/globalcatalog" Then
      objArgs("Port") = "GC"
    ElseIf LCase(strArg) = "/onelevel" Then
      objArgs("Scope") = "onelevel"
    End If
  Next

  Set GetArgs = objArgs
End Function

Function GetComputersFromAD(objArgs)
  ' Returns an array containing all computers in AD. Performed here so we do not have to maintain
  ' a connection to AD for any longer than necessary. Valid Ports are LDAP or GC.

  Dim objConnection : Set objConnection = CreateObject("ADODB.Connection")
  objConnection.Provider = "ADsDSOObject"
  objConnection.Open "Active Directory Provider"

  Dim objCommand : Set objCommand = CreateObject("ADODB.Command")
  Set objCommand.ActiveConnection = objConnection
  objCommand.Properties("Page Size") = 1000

  objCommand.CommandText = "<" & objArgs("Port") & "://" & objArgs("Server") & objArgs("BaseDN") & _
    ">;" & objArgs("LDAPFilter") & ";" & "dNSHostName;" & objArgs("Scope")

  Dim objRecordSet : Set objRecordSet = objCommand.Execute

  Dim arrComputers()
  Dim i : i = 0

  Do Until objRecordSet.EOF
    ReDim Preserve arrComputers(i)
    arrComputers(i) = objRecordSet.Fields("dNSHostName").Value
    i = i + 1
    objRecordSet.MoveNext
  Loop

  Set objRecordSet = Nothing
  Set objCommand = Nothing
  Set objConnection = Nothing

  GetComputersFromAD = arrComputers
End Function

Function BatchGenerator(arrComputers, objArgs)
  Dim intTotalComputers : intTotalComputers = UBound(arrComputers) + 1
  Dim intPerThread : intPerThread = objArgs("NumComputers")

  ' Reduce the maximum number per thread if it will leave threads in the pool doing nothing
  If (intTotalComputers / objArgs("NumThreads")) < objArgs("NumComputers") Then
    intPerThread = Round(intTotalComputers / objArgs("NumThreads"))
    If intPerThread = 0 Then intPerThread = 1
  End If

  Dim intCurrentBatchCount : intCurrentBatchCount = 0
  Dim intBatchNo : intBatchNo = 0
  Dim arrBatches() : ReDim arrBatches(intBatchNo)
  Dim strComputer
  For Each strComputer in arrComputers
    If intCurrentBatchCount = intPerThread Then
      intCurrentBatchCount = 0
      intBatchNo = intBatchNo + 1
      ReDim Preserve arrBatches(intBatchNo)
    End If

    intCurrentBatchCount = intCurrentBatchCount + 1
    arrBatches(intBatchNo) = arrBatches(intBatchNo) & strComputer & BATCH_DELIMITER
  Next
  For intBatchNo = 0 to UBound(arrBatches)
    arrBatches(intBatchNo) = Left(arrBatches(intBatchNo), Len(arrBatches(intBatchNo)) - 1)
  Next

  BatchGenerator = arrBatches
End Function

Sub ThreadManager(arrBatches, objArgs)
  ' Starts multiple script processes and waits for completion

  Dim objShell : Set objShell = CreateObject("WScript.Shell")

  ' Create the Thread Monitor - For debugging.
  Dim objThreadMonitor : Set objThreadMonitor = CreateObject("Scripting.Dictionary")

  ' Execute thread for each batch
  Dim i : i = 0
  Do Until i = (UBound(arrBatches) + 1)
    If GetProcessCount(objArgs("ChildScript"), objThreadMonitor) < objArgs("NumThreads") Then
      If ENABLE_BATCH_PROGRESS_MONITOR = True Then
        WScript.Echo "Starting Batch " & (i + 1) & " of " & (UBound(arrBatches) + 1)
      End If

      ' Command format for inv.vbs
      Dim strCommand : strCommand = "cscript.exe " & objArgs("ChildScript") & " /f:inv-"  & (i + 1) & ".xml" & " /s:" & arrBatches(i)
      If objArgs.Exists("ChildArguments") Then
        strCommand = strCommand & " " & objArgs("ChildArguments")
      End If
WScript.Echo "Executing command: " & strCommand

      objShell.Run strCommand, 0, False
      i = i + 1
    Else
      WScript.Sleep 10000
    End If
  Loop

  ' Wait for completion of remaining threads
  Do Until GetProcessCount(objArgs("ChildScript"), objThreadMonitor) = 0
    WScript.Sleep 10000
  Loop

  Set objThreadMonitor = Nothing

  Set objShell = Nothing
End Sub

Function GetProcessCount(strChildScript, objThreadMonitor)
  ' Returns the number of cscript or wscript processes executing ChildScript.

  Dim objWMI : Set objWMI = GetObject("winmgmts:\\.\root\cimv2")
  Dim colItems : Set colItems = _
    objWMI.ExecQuery("SELECT CommandLine, Name, ProcessId " & _
    "FROM Win32_Process WHERE Name LIKE '_script.exe' AND CommandLine LIKE '% " & _
    strChildScript & " %'")

  GetProcessCount = colItems.Count

  ' Debug / Thread Monitoring

  If ENABLE_THREAD_MONITOR = True Then
    Dim objTemp : Set objTemp = CreateObject("Scripting.Dictionary")

    Dim objItem
    For Each objItem in colItems
      objTemp.Add objItem.ProcessId, objItem.CommandLine
      If Not objThreadMonitor.Exists(objItem.ProcessId) Then
        objThreadMonitor.Add objItem.ProcessId, objItem.CommandLine
        WScript.Echo "Thread started: PID " & objItem.ProcessId
      End If
    Next

    Dim strProcessId
    For Each strProcessId in objThreadMonitor
      If Not objTemp.Exists(strProcessId) Then
        WScript.Echo "Thead finished: PID " & strProcessId
        objThreadMonitor.Remove strProcessId
      End If
    Next

    Set objTemp = Nothing
  End If

  Set colItems = Nothing
  Set objWMI = Nothing
End Function

Sub ExecuteFinal(objArgs)

  If objArgs.Exists("FinalCommand") Then
    Dim objShell : Set objShell = CreateObject("WScript.Shell")
    objShell.Run objArgs("FinalCommand"), 0, True
    Set objShell = Nothing
  End If
End Sub

'
' Main code
'

Dim objArgs : Set objArgs = GetArgs
Dim arrComputers : arrComputers = GetComputersFromAD(objArgs)
Dim arrBatches : arrBatches = BatchGenerator(arrComputers, objArgs)
ThreadManager arrBatches, objArgs
ExecuteFinal(objArgs)