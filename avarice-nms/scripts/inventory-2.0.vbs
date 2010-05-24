' A script to generate an XML formatted inventory
'
' Author:  Chris Dent
' Webpage: www.indented.co.uk
' Date:    18/05/2010
'
' Change Log:
' * (19/05/2010) Rich Rumble  :   Added END seperations
' * (21/05/2010) Chris Dent   :   Switched END to block comments before each section :)
' * (21/05/2010) Chris Dent   :   Added ProgramInformation to read from Uninstall key
' * (21/05/2010) Chris Dent   :   Added automatic conversion from "localhost" to "." for connections only.
' * (21/05/2010) Chris dent   :   Added IIS query set

Option Explicit

'
' =======================  Constants  =======================
'

Const WbemAuthenticationLevelPktPrivacy = 6

' XMLDOM Constants

Const NODE_ELEMENT = 1

' StdRegProv (Registry)

Const HKEY_LOCAL_MACHINE = &H80000002

'
' ====================  Global Variables  ===================
'

Dim arrComputers
Dim strUsername, strPassword
Dim strFileName
Dim objTests : Set objTests = CreateObject("Scripting.Dictionary")

'
' ===================  Argument Handling  ===================
'

Sub SortArgs
  Dim objArgs : Set objArgs = WScript.Arguments

  If objArgs.Named("u") <> "" Then strUsername = objArgs.Named("u")
  If objArgs.Named("p") <> "" Then strPassword = objArgs.Named("p")
  If objArgs.Named("s") <> "" Then arrComputers = Split(objArgs.Named("s"), ",")
  If objArgs.Named("f") <> "" Then strFileName = objArgs.Named("f")

  If strFileName = "" Then strFileName = "Default.xml"

  Dim strTests : strTests = "hd,pc,bios,vid,mem,nic,cpu,hwerr,os,srvc,av,fw,ie,grp,usr,qfe,prog,apppool,ftp,smtp,web"
  If objArgs.Named("c") <> "" Then strTests = objArgs.Named("c")

  Dim strTest
  For Each strTest in Split(strTests, ",")
    If Not objTests.Exists(strTest) Then objTests.Add strTest, ""
  Next
End Sub

'
' ===================  Xml Writer Class  ====================
'

Class XmlWriter
  '
  ' Properties
  '

  Private objXml
  Private objCurrentNode

  '
  ' Constructor
  '

  Private Sub Class_Initialize()
    Set objXml = CreateObject("MSXML2.DomDocument")
    Set objCurrentNode = objXml
  End Sub

  '
  ' Public Methods
  '

  Public Sub Save(strFileName)
    objXml.Save(strFileName)
  End Sub

  Public Sub OpenChild(strName)
    Dim objNode : Set objNode = objXml.CreateNode(NODE_ELEMENT, strName, "")
    Set objCurrentNode = objCurrentNode.AppendChild(objNode)
  End Sub

  Public Sub CloseChild()
    Set objCurrentNode = objCurrentNode.ParentNode
  End Sub

  Public Sub AddNode(strName, strText)
    Dim objNode : Set objNode = objXml.CreateNode(NODE_ELEMENT, strName, "")

    If strText <> VbNull Then objNode.Text = strText

    objCurrentNode.AppendChild(objNode)
  End Sub

  Public Function ToString()
    ToString = objXml.Xml
  End Function
End Class

'
' ==================  Connectivity Tests  ===================
'

Function Ping(strComputer)
  Dim objShell : Set objShell = CreateObject("WScript.Shell")
  Dim booCode : booCode = objShell.Run("Ping -n 3 -w 1000 " & strComputer, 0, True)
  If booCode = 0 Then
    Ping = True
  Else
    Ping = False
  End If
End Function

'
' ==============  Wmi Connection Management  ================
'

Function ConnectWmi(objErrXml, strComputer, strNamespace, strUsername, strPassword)

  If strNamespace = "" Then strNamespace = "root\cimv2"

  Dim objWmi

  On Error Resume Next : Err.Clear
  If strUsername <> "" And strPassword <> "" Then
    Dim objWbemLocator : Set objWbemLocator = CreateObject("WbemScripting.SWbemLocator")
    Set objWmi = objWbemLocator.ConnectServer(strComputer, strNamespace, strUsername, strPassword)
  Else
    Set objWmi = GetObject("winmgmts:\\" & strComputer & "\" & strNamespace)
  End If

   If Err.Number <> 0 Then
    objErrXml.OpenChild "ErrorRecord"
    objErrXml.AddNode "Username", strUsername
    objErrXml.AddNode "Namespace", strNamespace
    objErrXml.AddNode "Message", "WMI Connect Failed: " & Err.Description
    objErrXml.CloseChild()
  End If

 objWmi.Security_.authenticationLevel = WbemAuthenticationLevelPktPrivacy

  Set ConnectWmi = objWmi
  On Error Goto 0
End Function

'
' ===================  Wmi Query Wrapper ====================
'

Sub WmiToXml(objWmi, objXml, strNodeName, strClass, arrProperties, strFilter)

  Dim strWql
  strWql = "SELECT * FROM " & strClass

  If strFilter <> "" Then strWql = strWql & " WHERE " & strFilter

  On Error Resume Next
  Dim colItems : Set colItems = objWmi.ExecQuery(strWql)
  If Err.Number <> 0 Then

  Else

    Dim objItem

    For Each objItem in colItems

      objXml.OpenChild strNodeName

      Dim objProperty
      For Each objProperty in objItem.Properties_
        If InStr(1, Join(arrProperties, ";"), objProperty.Name, VbTextCompare) > 0 Or arrProperties(0) = "" Then
          If objProperty.CIMType = 101 Then

            objXml.AddNode objProperty.Name, ToDateTime(objProperty.Value)

          ElseIf objProperty.IsArray = True Then

            ' Test for Object entries
            If objProperty.CIMType = 13 Then
               objXml.OpenChild objProperty.Name

               Dim objSubObject, objSubProperty, i : i = 1
               For Each objSubObject in objProperty.Value
                 objXml.OpenChild "Element" & i : i = i + 1

                 For Each objSubProperty in objSubObject.Properties_
                   objXml.AddNode objSubProperty.Name, objSubProperty.Value
                 Next

                 objXml.CloseChild()
               Next

               objXml.CloseChild()

            Else

              objXml.AddNode objProperty.Name, Join(objProperty.Value, " ")

            End If

          Else

            objXml.AddNode objProperty.Name, Trim(objProperty.Value)

          End If
        End If
      Next

      objXml.CloseChild()
    Next
  End If
End Sub

'
' ===============  ToDateTime (DMTF to Date) ================
'

Function ToDateTime(strDmtfDate)
  ' Converts a DMTF string to a Universal Date Time string

  Dim objSWbemDateTime : Set objSWbemDateTime = CreateObject("WbemScripting.SWbemDateTime")

  objSWbemDateTime.Value = strDmtfDate

  ToDateTime = objSWbemDateTime.GetVarDate(False)
End Function

'
' ===============  Wmi: root\cimv2 Namespace ================
'

'
' ===========  Hard Disks (Win32_LogicalDisk)  ==============
'

Sub HDInformation(objWmi, objXml)

  ' Get Mapped Drive(s) Info

  Dim arrProperties

  objXml.OpenChild "drives"
  objXml.OpenChild "mapped"

  arrProperties = Array("Description", "DeviceID", "FreeSpace", "Name", "ProviderName", _
    "VolumeSerialNumber", "Size", "VolumeName")

  WmiToXml objWmi, objXml, "Drive", "Win32_LogicalDisk", arrProperties, "Description='Network Connection'"

  objXml.CloseChild()
  objXml.OpenChild "physical"

  arrProperties = Array("Description", "DeviceID", "FileSystem", "FreeSpace", "Name", _
    "VolumeSerialNumber", "Size", "VolumeName")

  WmiToXml objWmi, objXml, "Drive", "Win32_LogicalDisk", arrProperties, "Description='Local Fixed Disk'"

  objXml.CloseChild()
  objXml.CloseChild()

End Sub

'
' ==========  Computer (Win32_ComputerSystem)  ==============
'

Sub ComputerInformation(objWmi, objXml)

  Dim arrProperties : arrProperties = Array("Caption", "Description", "DomainRole", _
    "Domain", "Manufacturer", "Model", "Name", "CurrentTimeZone")

  WmiToXml objWmi, objXml, "Computer", "Win32_ComputerSystem", arrProperties, ""

End Sub

'
' =======  Operating System (Win32_OperatingSystem)  ========
'

Sub OSInformation(objWmi, objXml)

  Dim arrProperties : arrProperties = Array("CountryCode", "CSDVersion", "CSName", _
    "FreeSpaceInPagingFiles", "FreePhysicalMemory", "FreeVirtualMemory", "InstallDate", _
    "Locale", "LocalDateTime", "LastBootUpTime", "NumberOfUsers", "NumberOfProcesses", _
    "Organization", "OSLanguage", "OSType", "OSProductSuite", "ProductType", _
    "RegisteredUser", "SerialNumber", "ServicePackMajorVersion", "ServicePackMinorVersion", _
    "CurrentTimeZone", "TotalVirtualMemorySize", "TotalVisibleMemorySize", "Version")

  WmiToXml objWmi, objXml, "OperatingSystem", "Win32_OperatingSystem", arrProperties, ""

End Sub

'
' ===================  BIOS (Win32_Bios)  ===================
'

Sub BIOSInformation(objWmi, objXml)

  Dim arrProperties : arrProperties = Array("BuildNumber", "Description", "Manufacturer", "Name", _
    "ReleaseDate", "SerialNumber", "Version")

  WmiToXml objWmi, objXml, "Bios", "Win32_BIOS", arrProperties, ""

End Sub

'
' ==================  Video (Win32_Video)  ==================
'

Sub VideoInformation(objWmi, objXml)

  objXml.OpenChild "Video"

  Dim arrProperties : arrProperties = Array("Description", "Name", "RefreshRate", "VideoMode")

  WmiToXml objWmi, objXml, "Gpu", "Win32_DisplayControllerConfiguration", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' =======  Memory (Win32_LogicalMemoryConfiguration)  =======
'
'  ** This Class is only available in Windows 2000 and XP **
'  **     refer to Win32_OperatingSystem thereafter       **
'

Sub MemoryInformation(objWmi, objXml)

  Dim arrProperties : arrProperties = Array("AvailableVirtualMemory", "TotalPageFileSpace", _
    "TotalPhysicalMemory", "TotalVirtualMemory")

  WmiToXml objWmi, objXml, "Memory", "Win32_LogicalMemoryConfiguration", arrProperties, ""

End Sub

'
' =================  CPU (Win32_Processor)  =================
'

Sub CPUInformation(objWmi, objXml)

  objXml.OpenChild "Processor"

  Dim arrProperties : arrProperties = Array("Caption", "CurrentClockSpeed", "Description", _
    "DeviceID", "Family", "L2CacheSize", "LoadPercentage", "Manufacturer", "Name", "NumberOfCores", _
    "NumberOfLogicalProcessors")

  WmiToXml objWmi, objXml, "CPU", "Win32_Processor", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' =================  Hardware  (Win32_PNPEntity)  =================
'

Sub HWErrorInformation(objWmi, objXml)

  objXml.OpenChild "Errors"

  Dim arrProperties : arrProperties = Array("ConfigManagerErrorCode", "Description", "DeviceID", "Manufacturer", "Name")

  WmiToXml objWmi, objXml, "Err", "Win32_PNPEntity", arrProperties, "ConfigManagerErrorCode<>0"

  objXml.CloseChild()

End Sub

'
' ===============  Services (Win32_Service)  ================
'

Sub ServiceInformation(objWmi, objXml)

  objXml.OpenChild "Services"

  Dim arrProperties : arrProperties = Array("AcceptPause", "AcceptStop", "DesktopInteract", _
    "DisplayName", "ErrorControl", "Name", "PathName", "ProcessId", "Started", "StartMode", _
    "StartName", "State")

  WmiToXml objWmi, objXml, "Service", "Win32_Service", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ===========  Local Users (Win32_UserAccount)  =============
'

Sub UserInformation(objWmi, objXml)

  objXml.OpenChild "LocalUsers"

  Dim arrProperties : arrProperties = Array("AccountType", "Caption", "Description", _
    "Disabled", "Domain", "FullName", "Lockout", "Name", "PasswordChangeable", _
    "PasswordExpires", "PasswordRequired", "SID", "SIDType", "Status")

  WmiToXml objWmi, objXml, "User", "Win32_UserAccount", arrProperties, "LocalAccount=True"

  objXml.CloseChild()

End Sub

'
' ===  Quick Fix Engineering (Win32_QuickFixEngineering)  ===
'

Sub QFEInformation(objWmi, objXml)

  objXml.OpenChild "qfe_info"

  Dim arrProperties : arrProperties = Array("Caption", "CSName", "Description", "FixComments", _
    "HotFixID", "InstalledBy", "InstalledOn", "Name", "ServicePackInEffect", "Status")

  WmiToXml objWmi, objXml, "Fix", "Win32_QuickFixEngineering", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' =========  Wmi: root\cimv2 and root\wmi Namespace =========
'

'
' ======  Network (Win32_NetworkAdapterConfiguration)  ======
'               MSNdis_LinkSpeed for LinkSpeed
'

Sub NetworkInformation(objWmi_Cimv2, objWmi_RootWmi, objXml)

  objXml.OpenChild "Network"
  On Error Resume Next
  Dim colItems : Set colItems = objWmi_Cimv2.ExecQuery("SELECT * FROM " & _
    "Win32_NetworkAdapterConfiguration WHERE IPEnabled=True",, 48)

  Dim objItem
  For Each objItem in colItems

    objXml.OpenChild "Nic"

    objXml.AddNode "Caption", objItem.Caption
    objXml.AddNode "Description", objItem.Description
    objXml.AddNode "DhcpEnabled", objItem.DhcpEnabled
    objXml.AddNode "MacAddress", objItem.MacAddress
    objXml.AddNode "WINSPrimaryServer", objItem.WINSPrimaryServer
    objXml.AddNode "WINSSecondaryServer", objItem.WINSSecondaryServer

    Dim strDefaultIPGateway
    If Not IsNull(objItem.DefaultIPGateway) Then
      strDefaultIPGateway = Join(objItem.DefaultIPGateway, ",")
    End If
    objXml.AddNode "DefaultIPGateway", strDefaultIPGateway

    Dim strDnsSuffixSearchOrder
    If Not IsNull(objItem.DnsDomainSuffixSearchOrder) Then
       strDnsSuffixSearchOrder = Join(objItem.DnsDomainSuffixSearchOrder, ",")
    End If
    objXml.AddNode "DnsDomainSuffixSearchOrder", strDnsSuffixSearchOrder

    Dim objLinkSpeed : Set objLinkSpeed = objWmi_RootWmi.Get("MSNdis_LinkSpeed.InstanceName='" & _
      objItem.Description & "'")

    objXml.AddNode "LinkSpeed", objLinkSpeed.NDisLinkSpeed

    objXml.OpenChild "Addresses"

    Dim i
    For i = 0 To UBound(objItem.IPAddress)
      objXml.OpenChild "Address"

      objXml.AddNode "IPAddress", objItem.IPAddress(i)
      objXml.AddNode "Subnet", objItem.IPSubnet(i)

      objXml.CloseChild()

    Next

    objXml.CloseChild()
    objXml.CloseChild()

  Next

  objXml.CloseChild()
End Sub

'
' ===========  Wmi: root\default:StdRegProv Class ===========
'

'
' =============  Installed Software (StdRegProv)  ===========
'

Sub ProgramInformation(objWmi, objXml)

  objXml.OpenChild "Software"

  Dim arrProperties : arrProperties = Array("DisplayName", "InstallDate", "Publisher", _
    "DisplayVersion", "UninstallString")

  Dim strKeyPath : strKeyPath = "Software\Microsoft\Windows\CurrentVersion\Uninstall"

  Dim arrSubKeys
  objWmi.EnumKey HKEY_LOCAL_MACHINE, strKeyPath, arrSubKeys

  Dim strSubKey
  For Each strSubKey in arrSubKeys

    Dim strSubKeyPath : strSubKeyPath = strKeyPath & "\" & strSubKey

    Dim arrNames, arrTypes
    objWmi.EnumValues HKEY_LOCAL_MACHINE, strSubKeyPath, arrNames, arrTypes

    ' Drop empty keys
    If Not IsNull(arrNames) Then

      ' Drop keys with no DisplayName value
      If InStr(Join(arrNames), "DisplayName") > 0 Then

        objXml.OpenChild "program"

        Dim strProperty, strValue
        For Each strProperty in arrProperties
          objWmi.GetStringValue HKEY_LOCAL_MACHINE, strSubKeyPath, strProperty, strValue

          objXml.AddNode strProperty, strValue
        Next

        objXml.CloseChild()

      End If
    End If

  Next

  objXml.CloseChild()

End Sub

'
' ===========  Wmi: root\SecurityCenter Namespace ===========
'

'
' ==============  AntiVirus (AntivirusProduct)  =============
'

Sub AVInformation(objWmi, objXml)

  objXml.OpenChild "Antivirus"

  Dim arrProperties : arrProperties = Array("CompanyName", "DisplayName", "EnableOnAccessUIMd5Hash", _
    "EnableOnAccessUIParameters", "InstanceGuid", "OnAccessScanningEnabled", "PathToEnableOnAccessUI", _
    "PathToUpdateUI", "ProductUpToDate", "UpdateUIMd5Hash", "UpdateUIParameters", "VersionNumber")

  WmiToXml objWmi, objXml, "AV", "AntivirusProduct", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ===============  Firewall (FirewallProduct)  ==============
'

Sub FWInformation(objWmi, objXml)

  Dim arrProperties : arrProperties = Array("CompanyName", "DisplayName", "EnableUIMd5Hash", _
    "EnableUIParameters", "InstanceGuid", "PathToEnableOnUI", "VersionNumber")

  WmiToXml objWmi, objXml, "FW", "FirewallProduct", arrProperties, ""

End Sub

'
' ===  Wmi: root\cimv2\Applications\MicrosoftIE Namespace ===
'

'
' ==================  MSIE (MicrosoftIE_*)  =================
'

Sub MSIEInformation(objWmi, objXml)

  Dim arrProperties

  objXml.OpenChild "IE"

  arrProperties = Array("ActivePrinter", "Build", "Caption", "CipherStrength", "ContentAdvisor", _
    "Description", "IEAKInstall", "Language", "Name", "Path", "ProductID", "SettingID", "Version")

  WmiToXml objWmi, objXml, "Summary", "MicrosoftIE_Summary", arrProperties, ""

  arrProperties = Array("AutoConfigProxy", "AutoConfigURL", "Caption", "Description", "Proxy", _
    "ProxyOverride", "ProxyServer", "SettingID")

  WmiToXml objWmi, objXml, "LAN", "MicrosoftIE_LanSettings", arrProperties, ""

  objXml.OpenChild "Objects"

  arrProperties = Array("Caption", "CodeBase", "Description", "ProgramFile", "SettingID", "Status")

  WmiToXml objWmi, objXml, "BHO", "MicrosoftIE_Object", arrProperties, ""

  objXml.CloseChild()

  objXml.CloseChild()

End Sub

'
' ==================  Adsi: WinNT Provider  =================
'

'
' ==========  Group (WinNT://<System>/<Children>)  ==========
'

Sub GroupInformation(objXml, strComputer, strUsername, strPassword)

  Dim objComputer : Set objComputer = Nothing

  On Error Resume Next
  If strUsername <> "" And strPassword <> "" Then
    Dim objWinNT : Set objWinNT = GetObject("WinNT:")
    Set objComputer = objWinNT.OpenDsObject("WinNT://" & strComputer, strUsername, strPassword)
  Else
    Set objComputer = GetObject("WinNT://" & strComputer)
  End If
  On Error Goto 0

  If Not objComputer Is Nothing Then

    objXml.OpenChild "LocalGroups"

    objComputer.Filter = Array("group")

    Dim objGroup
    For Each objGroup in objComputer
      objXml.OpenChild "Group"

      objXml.AddNode "Name", objGroup.Name

      objXml.OpenChild "Members"

      Dim objMember
      For Each objMember in objGroup.Members
        objXml.OpenChild "Member"

        objXml.AddNode "Name",    objMember.Name
        objXml.AddNode "AdsPath", objMember.AdsPath
        objXml.AddNode "Class",   objMember.Class

        Dim strScope : strScope = "Local"
        If UBound(Split(objMember.ADSPath, "/")) = 3 And _
            InStr(objMember.ADSPath, "NT AUTHORITY") = 0 And _
            InStr(objMember.ADSPath, "NT SERVICE") = 0 Then

          strScope = "Domain"
        End If
        objXml.AddNode "Scope",   strScope

        objXml.CloseChild() ' Member
      Next

      objXml.CloseChild() ' Members
      objXml.CloseChild() ' Group
    Next
    objXml.CloseChild() ' LocalGroups
  End If
End Sub

'
' ===========  Wmi: root\MicrosoftIISv2 Namespace ===========
'

'
' =====  Application Pools (IIsApplicationPoolSetting)  =====
'

Sub ApplicationPools(objWmi, objXml)

  objXml.OpenChild "ApplicationPools"

  Dim arrProperties : arrProperties = Array("AppPoolIdentityType", "AppPoolState", _
    "Enable32BitAppOnWin64", "Name", "WAMUsername", "PeriodicRestartMemory", _
    "PeriodicRestartPrivateMemory", "PeriodicRestartRequests", "PeriodicRestartSchedule", _
    "PeriodicRestartTime", "PingingEnabled", "PingInterval", "RapidFailProtection", _
    "RapidFailProtectionInterval", "RapidFailProtectionMaxCrashes")

  WmiToXml objWmi, objXml, "ApplicationPool", "Win32_ComputerSystem", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ===========  FTP Servers (IIsFtpServerSetting)  ===========
'

Sub FtpServers(objWmi, objXml)

  objXml.OpenChild "FtpServers"

  Dim arrProperties : arrProperties = Array("AnonymousUserName", "AnonymousOnly", _
    "LogType", "LogFileDirectory", "LogFileTruncateSize", "LogFilePeriod", _
    "Name", "ServerComment", "ServerBindings")

  WmiToXml objWmi, objXml, "FtpServer", "IIsFtpServerSetting", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ==========  Smtp Servers (IIsSmtpServerSetting)  ==========
'

Sub SmtpServers(objWmi, objXml)

  objXml.OpenChild "SmtpServers"

  Dim arrProperties : arrProperties = Array("BadMailDirectory", "DefaultDomain", "DropDirectory", _
    "EnableReverseDnsLookup", "FullyQualifiedDomainName", "Name", "PickupDirectory", "QueueDirectory", _
    "RemoteSmtpPort", "SecureBindings", "SendBadTo", "ServerBindings", "ServerComment", "SmartHost")

  WmiToXml objWmi, objXml, "SmtpServer", "IIsSmtpServerSetting", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ===========  Web Servers (IIsWebServerSetting)  ===========
'

Sub WebServers(objWmi, objXml)

  objXml.OpenChild "WebServers"

  Dim arrProperties : arrProperties = Array("AccessExecute", "AccessFlags", "AccessScript", _
    "AccessRead", "AccessSource", "AccessWrite", "AnonymousUserName", "AppFriendlyName", _
    "AppPoolId", "AuthAnonymous", "AuthBasic", "AuthChangeDisable", "AuthChangeUnsecure", _
    "AuthFlags", "ContentIndexed", "DefaultDoc", "EnableDefaultDoc", "EnableDirBrowsing", _
    "FrontPageWeb", "LogType", "LogFileDirectory", "LogFileTruncateSize", "LogFilePeriod", _
    "Name", "ServerComment", "ServerBindings", "SecureBindings")

  WmiToXml objWmi, objXml, "WebServer", "IIsWebServerSetting", arrProperties, ""

  objXml.CloseChild()

End Sub

'
' ========================  Main Code =======================
'

SortArgs

' Object state initialisation

Dim objWmi                : Set objWmi = Nothing
Dim objWmi_RootWmi        : Set objWmi_RootWmi = Nothing
Dim objWmi_Reg            : Set objWmi_Reg = Nothing
Dim objWmi_SecurityCentre : Set objWmi_SecurityCentre = Nothing
Dim objWmi_MSIE           : Set objWmi_MSIE = Nothing
Dim objWmi_IIS            : Set objWmi_IIS = Nothing

' Initialise the XML documents

Dim objXml    : Set objXml = New XmlWriter
Dim objErrXml : Set objErrXml = New XmlWriter

' Create a root node
objXml.OpenChild "inventory" ' Root Element
objErrXml.OpenChild "errors"

Dim strComputer
For Each strComputer in arrComputers

  objXml.OpenChild "Computer"
  objXml.AddNode "Name", strComputer
  objXml.AddNode "ScanStarted", Now

  objErrXml.OpenChild "Computer"
  objErrXml.AddNode "Name", strComputer
  objErrXml.AddNode "ScanStarted", Now

  If Ping(strComputer) Then

    ' Change localhost to .
    If LCase(strComputer) = "localhost" Then strComputer = "."

    '
    ' Classes from Root\Cimv2
    '

    On Error Resume Next
    Set objWmi = ConnectWmi(objErrXml, strComputer, "", strUsername, strPassword)
    On Error Goto 0

    If Not objWmi Is Nothing Then

      If objTests.Exists("hd") Then    HDInformation       objWmi, objXml
      If objTests.Exists("pc") Then    ComputerInformation objWmi, objXml
      If objTests.Exists("os") Then    OSInformation       objWmi, objXml
      If objTests.Exists("bios") Then  BIOSInformation     objWmi, objXml
      If objTests.Exists("vid") Then   VideoInformation    objWmi, objXml
      If objTests.Exists("mem") Then   MemoryInformation   objWmi, objXml
      If objTests.Exists("cpu") Then   CPUInformation      objWmi, objXml
      If objTests.Exists("hwerr") Then HWErrorInformation  objWmi, objXml
      If objTests.Exists("srvc") Then  ServiceInformation  objWmi, objXml
      If objTests.Exists("usr") Then   UserInformation     objWmi, objXml
      If objTests.Exists("qfe") Then   QFEInformation      objWmi, objXml

      If objTests.Exists("nic") Then
        On Error Resume Next
        Set objWmi_RootWmi = ConnectWmi(objErrXml, strComputer, "root\wmi", strUsername, strPassword)
        On Error Goto 0

        If Not objWmi_RootWmi Is Nothing Then
          NetworkInformation objWmi, objWmi_RootWmi, objXml
        End If
      End If

    End If

    '
    ' Product Information from StdRegProv
    '

    If objTests.Exists("prog") Then
      On Error Resume Next
      Set objWmi_Reg = ConnectWmi(objErrXml, strComputer, "root\default:StdRegProv", strUsername, strPassword)
      On Error Goto 0

      If Not objWmi_Reg Is Nothing Then
        ProgramInformation objWmi_Reg, objXml
      End If

    End If

    '
    ' Classes from root\SecurityCenter
    '

    On Error Resume Next
    Set objWmi_SecurityCentre = ConnectWmi(objErrXml, strComputer, "root\SecurityCenter", strUsername, strPassword)
    On Error Goto 0

    If Not objWmi_SecurityCentre Is Nothing Then
      If objTests.Exists("av") Then AVInformation objWmi_SecurityCentre, objXml
      If objTests.Exists("fw") Then FWInformation objWmi_SecurityCentre, objXml
    End If

    '
    ' Classes from root\Cimv2\Applications\MicrosoftIE
    '

    If objTests.Exists("ie") Then

      On Error Resume Next
      Set objWmi_MSIE = ConnectWmi(objErrXml, strComputer, "root\cimv2\Applications\MicrosoftIE", strUsername, strPassword)
      On Error Goto 0

      If Not objWmi_MSIE Is Nothing Then
        MSIEInformation objWmi_MSIE, objXml
      End If

    End If

    '
    ' ADSI
    '

    If objTests.Exists("grp") Then GroupInformation objXml, strComputer, strUsername, strPassword

    '
    ' Classes from root\MicrosoftIISv2
    '

    On Error Resume Next
    Set objWmi_IIS = ConnectWmi(objErrXml, strComputer, "root\MicrosoftIISv2", strUsername, strPassword)
    On Error Goto 0

    If Not objWmi_IIS Is Nothing Then

      If objTests.Exists("apppool") Then ApplicationPools objWmi_IIS, objXml
      If objTests.Exists("ftp") Then     FtpServers       objWmi_IIS, objXml
      If objTests.Exists("smtp") Then    SmtpServers      objWmi_IIS, objXml
      If objTests.Exists("web") Then     WebServers       objWmi_IIS, objXml

    End If

  Else

    objErrXml.OpenChild "ErrorRecord"
    objErrXml.AddNode "Ping", "Failed"
    objErrXml.CloseChild()

  End If

  '
  ' Cleanup connections
  '

  Set objWmi = Nothing
  Set objWmi_RootWmi = Nothing
  Set objWmi_Reg = Nothing
  Set objWmi_SecurityCentre = Nothing
  Set objWmi_MSIE = Nothing
  Set objWmi_IIS = Nothing

  objXml.AddNode "ScanEnded", Now
  objErrXml.AddNode "ScanEnded", Now

  objXml.CloseChild()
  objErrXml.CloseChild()
Next

objXml.CloseChild() ' Root Element

' WScript.Echo objXml.ToString()

objXml.Save(strFileName)

If InStr(strFileName, "\") > 0 Then
  Dim strTemp : strTemp = Mid(strFileName,  1, InStrRev(strFileName, "\"))
  strFileName = strTemp & "Err-" & Replace(strFileName, strTemp, "")
End If

objErrXml.Save(strFileName)