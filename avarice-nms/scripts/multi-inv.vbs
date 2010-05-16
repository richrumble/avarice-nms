'Usage:
'cscript /nologo inv.vbs /s:ip.ip.ip.ip  /u:user_name /p:password /c:hd,pc,bios,vid,mem,nic,cpu,hwerr,os,srvc,av,fw,ie,grp,usr,qfe,prog
' ip's can be comma separated... ip's could also be dns-name(s)
' if no parameters are given local host and all parameters are run...

Const WbemAuthenticationLevelPktPrivacy = 6

Function Ping(strComputer)
  Dim objShell : Set objShell = CreateObject("WScript.Shell")
  Dim booCode : booCode = objShell.Run("Ping -n 3 -w 1000 " & strComputer, 0, True)
  If booCode = 0 Then
    Ping = True
  Else
    Ping = False
  End If
End Function

Function CleanUpXML(ByVal szInput)
    Dim szOutput
    szOutput = Trim(szInput)    'REMOVE LEADING/TRAILING SPACES
    szOutput = Replace(szOutput, "'", "")
    szOutput = Replace(szOutput, """", "")
    szOutput = Replace(szOutput, "&", "&amp;")
    szOutput = Replace(szOutput, "<", "&lt;")
    szOutput = Replace(szOutput, ">", "&gt;")
    CleanUpXML = szOutput
End Function

If Wscript.Arguments.Named("u") <> "" And Wscript.Arguments.Named("p") <> "" Then
    boolAlternate = True
    strUser = WScript.Arguments.Named("u")
    strPassword = WScript.Arguments.Named("p")
'    objFile.WriteLine "Using alternate credentials..."
Else
    boolAlternate = False
'    objFile.WriteLine "Using current credentials..."
End If

If WScript.Arguments.Named("s") = "" Then
    arrComputers = Array("localhost")
Else
    arrComputers = Split(WScript.Arguments.Named("s"), ",")

End If

Dim strFileName : strFileName = WScript.Arguments.Named("File")

Dim objFileSystem : Set objFileSystem = CreateObject("Scripting.FileSystemObject")
Dim objFile : Set objFile = objFileSystem.OpenTextFile(strFileName, 2, True, 0)

objFile.WriteLine "<?xml version=" & Chr(34) &  "1.0" & Chr(34) & " ?>"
objFile.WriteLine "<Inventory>"
objFile.WriteLine "<!-- Script-BOF:" & DateDiff("s", "12/31/1970 00:00:00", Now) & " -->"

For Each strComputer In arrComputers
  If Ping(strComputer) = True Then
  objFile.WriteLine "  <Computer ID=" & Chr(34) & strComputer & Chr(34) & ">"
  objFile.WriteLine "  <!-- Computer-BOF:" & DateDiff("s", "12/31/1970 00:00:00", Now) & " -->"
    If boolAlternate = True Then
        strNamespace = "root\cimv2"
        Set objWbemLocator = CreateObject("WbemScripting.SWbemLocator")
        Set objWMIService = objwbemLocator.ConnectServer(strComputer, strNamespace, strUser, strPassword)
        objWMIService.Security_.authenticationLevel = WbemAuthenticationLevelPktPrivacy
    Else
        Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")
    End If
  Else
  End If


    If Wscript.Arguments.Named("c") <> "" Then
        ' This splits the "/c:" argument by a comma, and goes through each
        arrSections = Split(Wscript.Arguments.Named("c"), ",")
        For Each strSection In arrSections
            Select Case Trim(LCase(strSection))
                Case "hd"
                    HD_Info
                Case "pc"
                    PC_Info
                Case "bios"
                    Bios_Info
                Case "vid"
                    Video_Info
                Case "mem"
                    Memory_Info
                Case "nic"
                    NIC_Info
                Case "cpu"
                    CPU_Info
                Case "hwerr"
                    HW_Err_Info
                Case "os"
                    OS_Info
                Case "srvc"
                    SRVC_Info
                Case "av"
                    AV_Info
                Case "fw"
                    FW_Info
                Case "ie"
                    IE_Info
                Case "grp"
                    Grp_Info
                Case "usr"
                    Usr_Info
                Case "qfe"
                    QFE_Info
                Case "prog"
                    Prog_Info
            End Select
        Next

objFile.WriteLine "  <!-- Computer-EOF:" & DateDiff("s", "12/31/1970 00:00:00", Now) & " -->"
objFile.WriteLine "  </Computer>"

    Else
'       This is where all tests are run
        HD_Info
        PC_Info
        Bios_Info
        Video_Info
        Memory_Info
        NIC_Info
        CPU_Info
        HW_Err_Info
        OS_Info
        SRVC_Info
        AV_Info
        FW_Info
        IE_Info
        Grp_Info
        Usr_Info
        QFE_Info
        Prog_Info
    End If

objFile.WriteLine "  <!-- Computer-EOF:" & DateDiff("s", "12/31/1970 00:00:00", Now) & " -->"
objFile.WriteLine "  </Computer>"

Next

Sub HD_Info
On Error Resume Next
'    Get Mapped Drive(s) Info
    objFile.WriteLine  "    <Drives>"
    objFile.WriteLine  "      <Mapped>"
    Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalDisk Where Description = 'Network Connection'")
    For Each objItem in colItems
        objFile.WriteLine  "        <HD>"
        objFile.WriteLine  "          <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "          <DeviceID>" & objItem.DeviceID & "</DeviceID>"
        objFile.WriteLine  "          <FileSystem>" & objItem.FileSystem & "</FileSystem>"
        objFile.WriteLine  "          <FreeSpace>" & objItem.FreeSpace & "</FreeSpace>"
        objFile.WriteLine  "          <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "          <ProviderName>" & objItem.ProviderName & "</ProviderName>"
        objFile.WriteLine  "          <SerialNumber>" & objItem.VolumeSerialNumber & "</SerialNumber>"
        objFile.WriteLine  "          <Size>" & objItem.Size & "</Size>"
        objFile.WriteLine  "          <VolumeName>" & objItem.VolumeName & "</VolumeName>"
        objFile.WriteLine  "        </HD>"
    Next
    objFile.WriteLine  "      </Mapped>"
    objFile.WriteLine  "      <Physical>"
'    Get Harddrive(s) Info
    Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalDisk Where Description = 'Local Fixed Disk'")
    For Each objItem in colItems
        objFile.WriteLine  "        <HD>"
        objFile.WriteLine  "          <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "          <DeviceID>" & objItem.DeviceID & "</DeviceID>"
        objFile.WriteLine  "          <FileSystem>" & objItem.FileSystem & "</FileSystem>"
        objFile.WriteLine  "          <FreeSpace>" & objItem.FreeSpace & "</FreeSpace>"
        objFile.WriteLine  "          <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "          <SerialNumber>" & objItem.VolumeSerialNumber & "</SerialNumber>"
        objFile.WriteLine  "          <Size>" & objItem.Size & "</Size>"
        objFile.WriteLine  "          <VolumeName>" & objItem.VolumeName & "</VolumeName>"
        objFile.WriteLine  "        </HD>"
    Next
    objFile.WriteLine  "      </Physical>"
    objFile.WriteLine  "    </Drives>"
    Set colItems = Nothing
End Sub
'    Get Local Login Information
Sub PC_Info
    objFile.WriteLine  "    <Login>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_ComputerSystem",,48)
    For Each objItem in colItems
        objFile.WriteLine  "      <Caption>" & objItem.Caption & "</Caption>"
        objFile.WriteLine  "      <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "      <DomainRole>" & objItem.DomainRole & "</DomainRole>"
        objFile.WriteLine  "      <Domain>" & objItem.Domain & "</Domain>"
        objFile.WriteLine  "      <Manufacturer>" & objItem.Manufacturer & "</Manufacturer>"
        objFile.WriteLine  "      <Model>" & objItem.Model & "</Model>"
        objFile.WriteLine  "      <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "      <TimeZone>" & objItem.CurrentTimeZone & "</TimeZone>"
        objFile.WriteLine  "      <UserName>" & objItem.UserName & "</UserName>"
    Next
    objFile.WriteLine  "    </Login>"
    Set colItems = Nothing
End Sub

'    Get Bios info, includng Asset Tag, Bios Revision and Manufacturer
Sub Bios_Info
    objFile.WriteLine  "    <Bios>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_BIOS",,48)
    For Each objItem in colItems
        objFile.WriteLine  "      <BuildNumber>" & objItem.BuildNumber & "</BuildNumber>"
        objFile.WriteLine  "      <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "      <Manufacturer>" & objItem.Manufacturer & "</Manufacturer>"
        objFile.WriteLine  "      <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "      <ReleaseDate>" & objItem.ReleaseDate & "</ReleaseDate>"
        objFile.WriteLine  "      <SerialNumber>" & objItem.SerialNumber & "</SerialNumber>"
        objFile.WriteLine  "      <Version>" & objItem.Version & "</Version>"
    Next
    objFile.WriteLine  "    </Bios>"
    Set colItems = Nothing
End Sub

'    Get Screen Resolution, Refreash and Video Card info
Sub Video_Info
     objFile.WriteLine  "    <Video>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_DisplayControllerConfiguration",,48)
    For Each objItem in colItems
        objFile.WriteLine  "      <Gpu>"
        objFile.WriteLine  "        <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "        <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "        <RefreshRate>" & objItem.RefreshRate & "</RefreshRate>"
        objFile.WriteLine  "        <VideoMode>" & objItem.VideoMode & "</VideoMode>"
        objFile.WriteLine  "      </Gpu>"
    Next
    objFile.WriteLine  "    </Video>"
End Sub

'    Get Memory Information
Sub Memory_Info
    objFile.WriteLine  "    <Mem>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalMemoryConfiguration",,48)
    For Each objItem in colItems
        objFile.WriteLine  "      <FreeVirtMem>" & objItem.AvailableVirtualMemory & "</FreeVirtMem>"
        objFile.WriteLine  "      <TotPgFileSz>" & objItem.TotalPageFileSpace & "</TotPgFileSz>"
        objFile.WriteLine  "      <TotPhyMem>" & objItem.TotalPhysicalMemory & "</TotPhyMem>"
        objFile.WriteLine  "      <TotVirtMem>" & objItem.TotalVirtualMemory & "</TotVirtMem>"
    Next
    objFile.WriteLine  "    </Mem>"
End Sub

'    Get NIC information
Sub NIC_Info
  On Error Resume Next
    objFile.WriteLine  "    <Network>"
    Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")
    Set colNICConfig = objWMIService.ExecQuery("SELECT * FROM Win32_NetworkAdapterConfiguration WHERE IPEnabled=TRUE")
    For Each objNICConfig in colNICConfig
        objFile.WriteLine "      <Nic>"
        objFile.WriteLine "        <Caption>" & objNICConfig.Caption & "</Caption>"
      If Not IsNull(objNICConfig.DNSDomainSuffixSearchOrder) Then
        Dim strDefaultIPGateway
        strDefaultIPGateway = Join(objNICConfig.DefaultIPGateway, ",")
        objFile.WriteLine "        <DfltGtwy>" & strDefaultIPGateway & "</DfltGtwy>"
      End If
        objFile.WriteLine "        <Description>" & objNICConfig.Description & "</Description>"
      If Not IsNull(objNICConfig.DNSDomainSuffixSearchOrder) Then
      Dim strDNSDomainSuffixSearchOrder
      strDNSDomainSuffixSearchOrder = Join(objNICConfig.DNSDomainSuffixSearchOrder, ",")
        objFile.WriteLine "        <DNSSrchOrdr>" & strDNSDomainSuffixSearchOrder & "</DNSSrchOrdr>"
      End If
        objFile.WriteLine "        <DHCPEnabled>" & objNICConfig.DHCPEnabled & "</DHCPEnabled>"
        objFile.WriteLine "        <MACAddress>" & objNICConfig.MACAddress & "</MACAddress>"
        objFile.WriteLine "        <WINSPriSrvr>" & objNICConfig.WINSPrimaryServer & "</WINSPriSrvr>"
        objFile.WriteLine "        <WINSSecSrvr>" & objNICConfig.WINSSecondaryServer & "</WINSSecSrvr>"
     Dim i
      For i = 0 To UBound (objNICConfig.IPAddress)
        objFile.WriteLine "        <IPAddress>" & objNICConfig.IPAddress(i) & "</IPAddress>"
        objFile.WriteLine "        <Subnet>" & objNICConfig.IPSubnet(i) & "</Subnet>"
      Next
      Set objWMI = GetObject("winmgmts:\\" & strComputer & "\root\wmi")
      Set objLinkSpeed = objWMI.Get("MSNdis_LinkSpeed.InstanceName='" & objNICConfig.Description & "'")
        objFile.WriteLine "        <LinkSpeed>" & objLinkSpeed.NdisLinkSpeed & "</LinkSpeed>"
        objFile.WriteLine "      </Nic>"
    Next
    objFile.WriteLine  "    </Network>"
End Sub

'    Get CPU Information
'    Intel Core 2 Duo and Quad don't return an instance for each core, just one.
Sub CPU_Info
    objFile.WriteLine  "    <Processor>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_Processor",,48)
    For Each objItem in colItems
        objFile.WriteLine  "      <Cpu>"
        objFile.WriteLine  "        <Caption>" & objItem.Caption & "</Caption>"
        objFile.WriteLine  "        <ClockSpeed>" & objItem.CurrentClockSpeed & "</ClockSpeed>"
        objFile.WriteLine  "        <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "        <DeviceID>" & objItem.DeviceID & "</DeviceID>"
        objFile.WriteLine  "        <Family>" & objItem.Family & "</Family>"
        objFile.WriteLine  "        <L2CacheSize>" & objItem.L2CacheSize & "</L2CacheSize>"
        objFile.WriteLine  "        <Load>" & objItem.LoadPercentage & "</Load>"
        objFile.WriteLine  "        <Manufacturer>" & objItem.Manufacturer & "</Manufacturer>"
        objFile.WriteLine  "        <Name>" & CleanUpXML(objItem.Name) & "</Name>"
        objFile.WriteLine  "        <NumofCores>" & objItem.NumberOfCores & "</NumofCores>"
        objFile.WriteLine  "        <NumofLogicalCores>" & objItem.NumberOfLogicalProcessors & "</NumofLogicalCores>"
        objFile.WriteLine  "      </Cpu>"
    Next
    objFile.WriteLine  "    </Processor>"
End Sub

'    Show non-functioning Devices (yellow exclamation point, or red circle)
Sub HW_Err_Info
    objFile.WriteLine  "    <Errors>"
On Error Resume Next
    Set colItems = objWMIService.ExecQuery("Select * from Win32_PNPEntity Where ConfigManagerErrorCode <> 0")
      For Each objItem in colItems
        objFile.WriteLine  "      <Err>"
        objFile.WriteLine  "        <Description>" & objItem.Description & "</Description>"
        objFile.WriteLine  "        <DeviceID>" & CleanUpXML(objItem.DeviceID) & "</DeviceID>"
        objFile.WriteLine  "        <Manufacturer>" & objItem.Manufacturer & "</Manufacturer>"
        objFile.WriteLine  "        <ErrorCode>" & objItem.ConfigManagerErrorCode & "</ErrorCode>"
        objFile.WriteLine  "        <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine  "      </Err>"
     Next
    objFile.WriteLine  "    </Errors>"
End Sub

Sub OS_Info
On Error Resume Next
  objFile.WriteLine  "    <PC_Info>"
    Set colItems = objWMIService.ExecQuery("Select * from Win32_OperatingSystem",,48)
      For Each objItem in colItems
        objFile.WriteLine "      <Caption>" & objItem.Caption & "</Caption>"
        objFile.WriteLine "      <CountryCode>" & objItem.CountryCode & "</CountryCode>"
        objFile.WriteLine "      <CSDVersion>" & objItem.CSDVersion & "</CSDVersion>"
        objFile.WriteLine "      <CSName>" & objItem.CSName & "</CSName>"
        objFile.WriteLine "      <FreePgFlSpc>" & objItem.FreeSpaceInPagingFiles & "</FreePgFlSpc>"
        objFile.WriteLine "      <FreePhysMem>" & objItem.FreePhysicalMemory & "</FreePhysMem>"
        objFile.WriteLine "      <FreeVirtMem>" & objItem.FreeVirtualMemory & "</FreeVirtMem>"
        objFile.WriteLine "      <InstallDate>" & objItem.InstallDate & "</InstallDate>"
        objFile.WriteLine "      <Locale>" & objItem.Locale & "</Locale>"
        objFile.WriteLine "      <LocalTime>" & objItem.LocalDateTime & "</LocalTime>"
        objFile.WriteLine "      <LstBootTime>" & objItem.LastBootUpTime & "</LstBootTime>"
        objFile.WriteLine "      <NumOfUsers>" & objItem.NumberOfUsers & "</NumOfUsers>"
        objFile.WriteLine "      <NumProcess>" & objItem.NumberOfProcesses & "</NumProcess>"
        objFile.WriteLine "      <Org>" & objItem.Organization & "</Org>"
        objFile.WriteLine "      <OSLanguage>" & objItem.OSLanguage & "</OSLanguage>"
        objFile.WriteLine "      <OSType>" & objItem.OSType & "</OSType>"
        objFile.WriteLine "      <ProductSuite>" & objItem.OSProductSuite & "</ProductSuite>"
        objFile.WriteLine "      <ProductType>" & objItem.ProductType & "</ProductType>"
        objFile.WriteLine "      <RegisteredUsr>" & objItem.RegisteredUser & "</RegisteredUsr>"
        objFile.WriteLine "      <SerialNum>" & objItem.SerialNumber & "</SerialNum>"
        objFile.WriteLine "      <SPMajorVer>" & objItem.ServicePackMajorVersion & "</SPMajorVer>"
        objFile.WriteLine "      <SPMinorVer>" & objItem.ServicePackMinorVersion & "</SPMinorVer>"
        objFile.WriteLine "      <TimeZone>" & objItem.CurrentTimeZone & "</TimeZone>"
        objFile.WriteLine "      <TotVirtMem>" & objItem.TotalVirtualMemorySize & "</TotVirtMem>"
        objFile.WriteLine "      <TotVisMem>" & objItem.TotalVisibleMemorySize & "</TotVisMem>"
        objFile.WriteLine "      <Version>" & objItem.Version & "</Version>"
     Next
objFile.WriteLine  "    </PC_Info>"
End Sub

Sub SRVC_Info
On Error Resume Next
       objFile.WriteLine  "    <Service_Info>"
    Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_Service",,48)
    For Each objItem In colItems
        objFile.WriteLine "      <Service>"
        objFile.WriteLine "        <AcceptPause>" & objItem.AcceptPause & "</AcceptPause>"
        objFile.WriteLine "        <AcceptStop>" & objItem.AcceptStop & "</AcceptStop>"
        objFile.WriteLine "        <DesktopInteract>" & objItem.DesktopInteract & "</DesktopInteract>"
        objFile.WriteLine "        <DisplayName>" & objItem.DisplayName & "</DisplayName>"
        objFile.WriteLine "        <ErrorControl>" & objItem.ErrorControl & "</ErrorControl>"
        objFile.WriteLine "        <Name>" & objItem.Name & "</Name>"
        objFile.WriteLine "        <PathName>" & objItem.PathName & "</PathName>"
        objFile.WriteLine "        <ProcessId>" & objItem.ProcessId & "</ProcessId>"
        objFile.WriteLine "        <Started>" & objItem.Started & "</Started>"
        objFile.WriteLine "        <StartMode>" & objItem.StartMode & "</StartMode>"
        objFile.WriteLine "        <StartName>" & objItem.StartName & "</StartName>"
        objFile.WriteLine "        <State>" & objItem.State & "</State>"
        objFile.WriteLine "      </Service>"
    Next
  objFile.WriteLine  "    </Service_Info>"
End Sub


Sub AV_Info
On Error Resume Next
    objFile.WriteLine  "    <Anti-Virus>"
    Set oWMIAV = GetObject ("winmgmts:\\" & strComputer & "\root\SecurityCenter")
    Set colItems = oWMIAV.ExecQuery("Select * from AntiVirusProduct",,48)
    If Err = 0 Then
     For Each objAVProduct In colItems
        objFile.WriteLine "      <Av>"
        objFile.WriteLine "        <companyName>" & objAVProduct.companyName & "</companyName>"
        objFile.WriteLine "        <displayName>" & objAVProduct.displayName & "</displayName>"
        objFile.WriteLine "        <enableOnAccessUIMd5Hash>" & objAVProduct.enableOnAccessUIMd5Hash & "</enableOnAccessUIMd5Hash>"
        objFile.WriteLine "        <enableOnAccessUIParameters>" & objAVProduct.enableOnAccessUIParameters & "</enableOnAccessUIParameters>"
        objFile.WriteLine "        <instanceGuid>" & objAVProduct.instanceGuid & "</instanceGuid>"
        objFile.WriteLine "        <onAccessScanningEnabled>" & objAVProduct.onAccessScanningEnabled & "</onAccessScanningEnabled>"
        objFile.WriteLine "        <pathToEnableOnAccessUI>" & objAVProduct.pathToEnableOnAccessUI & "</pathToEnableOnAccessUI>"
        objFile.WriteLine "        <pathToUpdateUI>" & objAVProduct.pathToUpdateUI & "</pathToUpdateUI>"
        objFile.WriteLine "        <productUptoDate>" & objAVProduct.productUptoDate & "</productUptoDate>"
        objFile.WriteLine "        <updateUIMd5Hash>" & objAVProduct.updateUIMd5Hash & "</updateUIMd5Hash>"
        objFile.WriteLine "        <updateUIParameters>" & objAVProduct.updateUIParameters & "</updateUIParameters>"
        objFile.WriteLine "        <versionNumber>" & objAVProduct.versionNumber & "</versionNumber>"
        objFile.WriteLine "      </Av>"
    Next
    Else
      Err.Clear
        objFile.WriteLine "    <error>"
        objFile.WriteLine "      <Number>" & Err.Number & "</Number>"
        objFile.WriteLine "      <Source>" & Err.Source & "</Source>"
        objFile.WriteLine "      <Description>" & Err.Description & "</Description>"
        objFile.WriteLine "    </error>"
   End If
   objFile.WriteLine  "    </Anti-Virus>"
End Sub

Sub FW_Info
  objFile.WriteLine  "    <Firewall>"
On Error Resume Next
    Set oWMIFW = GetObject ("winmgmts:\\" & strComputer & "\root\SecurityCenter")
    Set colItems = oWMIFW.ExecQuery("Select * from FirewallProduct",,48)
    If Err = 0 Then
    For Each objFWProduct In colItems
        objFile.WriteLine "    <FW>"
        objFile.WriteLine "      <companyName>" & objFWProduct.companyName & "</companyName>"
        objFile.WriteLine "      <displayName>" & objFWProduct.displayName & "</displayName>"
        objFile.WriteLine "      <enableUIMd5Hash>" & objFWProduct.enableUIMd5Hash & "</enableUIMd5Hash>"
        objFile.WriteLine "      <enableUIParameters>" & objFWProduct.enableUIParameters & "</enableUIParameters>"
        objFile.WriteLine "      <instanceGuid>" & objFWProduct.instanceGuid & "</instanceGuid>"
        objFile.WriteLine "      <pathToEnableOnUI>" & objFWProduct.pathToEnableOnUI & "</pathToEnableOnUI>"
        objFile.WriteLine "      <versionNumber>" & objFWProduct.versionNumber & "</versionNumber>"
        objFile.WriteLine "    </FW>"
   Next
   Else
     Err.Clear
        objFile.WriteLine "    <error>"
        objFile.WriteLine "      <Number>" & Err.Number & "</Number>"
        objFile.WriteLine "      <Source>" & Err.Source & "</Source>"
        objFile.WriteLine "      <Description>" & Err.Description & "</Description>"
        objFile.WriteLine "    </error>"
   End If
    objFile.WriteLine  "    </Firewall>"
End Sub

Sub IE_Info
On Error Resume Next
    objFile.WriteLine  "    <IE>"
    objFile.WriteLine "      <Summary>"
 Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\CIMV2\Applications\MicrosoftIE")
   Set colItems = objWMIService.ExecQuery("SELECT * FROM MicrosoftIE_Summary",,48)
   For Each objItem In colItems
      objFile.WriteLine "        <ActivePrinter>" & objItem.ActivePrinter & "</ActivePrinter>"
      objFile.WriteLine "        <Build>" & objItem.Build & "</Build>"
      objFile.WriteLine "        <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "        <CipherStrength>" & objItem.CipherStrength & "</CipherStrength>"
      objFile.WriteLine "        <ContentAdvisor>" & objItem.ContentAdvisor & "</ContentAdvisor>"
      objFile.WriteLine "        <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "        <IEAKInstall>" & objItem.IEAKInstall & "</IEAKInstall>"
      objFile.WriteLine "        <Language>" & objItem.Language & "</Language>"
      objFile.WriteLine "        <Name>" & objItem.Name & "</Name>"
      objFile.WriteLine "        <Path>" & objItem.Path & "</Path>"
      objFile.WriteLine "        <ProductID>" & objItem.ProductID & "</ProductID>"
      objFile.WriteLine "        <SettingID>" & objItem.SettingID & "</SettingID>"
      objFile.WriteLine "        <Version>" & objItem.Version & "</Version>"
   Next
      objFile.WriteLine "      </Summary>"

      objFile.WriteLine "      <Lan>"
   Set colItems = objWMIService.ExecQuery("SELECT * FROM MicrosoftIE_LanSettings",,48)
   For Each objItem In colItems
      objFile.WriteLine "        <AutoConfigProxy>" & objItem.AutoConfigProxy & "</AutoConfigProxy>"
      objFile.WriteLine "        <AutoConfigURL>" & objItem.AutoConfigURL & "</AutoConfigURL>"
      objFile.WriteLine "        <AutoProxyDetectMode>" & objItem.AutoProxyDetectMode & "</AutoProxyDetectMode>"
      objFile.WriteLine "        <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "        <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "        <Proxy>" & objItem.Proxy & "</Proxy>"
      objFile.WriteLine "        <ProxyOverride>" & objItem.ProxyOverride & "</ProxyOverride>"
      objFile.WriteLine "        <ProxyServer>" & objItem.ProxyServer & "</ProxyServer>"
      objFile.WriteLine "        <SettingID>" & objItem.SettingID & "</SettingID>"
   Next
      objFile.WriteLine "      </Lan>"

      objFile.WriteLine "      <Objects>"
   Set colItems = objWMIService.ExecQuery("SELECT * FROM MicrosoftIE_Object",, 48)
   For Each objItem In colItems
      objFile.WriteLine "        <BHO>"
      objFile.WriteLine "          <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "          <CodeBase>" & objItem.CodeBase & "</CodeBase>"
      objFile.WriteLine "          <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "          <ProgramFile>" & objItem.ProgramFile & "</ProgramFile>"
      objFile.WriteLine "          <SettingID>" & objItem.SettingID & "</SettingID>"
      objFile.WriteLine "          <Status>" & objItem.Status & "</Status>"
      objFile.WriteLine "        </BHO>"
   Next
      objFile.WriteLine "      </Objects>"
      objFile.WriteLine "    </IE>"
End Sub

Sub Grp_Info
On Error Resume Next
   objFile.WriteLine "    <local_grps>"
   Set colGroups = GetObject("WinNT://" & strComputer & "")
   colGroups.Filter = Array("group")
   For Each objGroup In colGroups
      objFile.WriteLine "      <group>"
      objFile.WriteLine "        <grp_name>" & objGroup.Name & "</grp_name>"
      For Each objUser in objGroup.Members
        objFile.WriteLine "        <member>" & objUser.Name & "</member>"
    Next
    objFile.WriteLine "      </group>"
  Next
  objFile.WriteLine "    </local_grps>"
End Sub

Sub Usr_Info
On Error Resume Next
   Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\CIMV2")
   Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_UserAccount WHERE LocalAccount = True",,48)
   objFile.WriteLine "    <local_usr>"
   For Each objItem In colItems
      objFile.WriteLine "      <user>"
      objFile.WriteLine "        <AccountType>" & objItem.AccountType & "</AccountType>"
      objFile.WriteLine "        <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "        <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "        <Disabled>" & objItem.Disabled & "</Disabled>"
      objFile.WriteLine "        <Domain>" & objItem.Domain & "</Domain>"
      objFile.WriteLine "        <FullName>" & objItem.FullName & "</FullName>"
      objFile.WriteLine "        <Lockout>" & objItem.Lockout & "</Lockout>"
      objFile.WriteLine "        <Name>" & objItem.Name & "</Name>"
      objFile.WriteLine "        <PasswordChangeable>" & objItem.PasswordChangeable & "</PasswordChangeable>"
      objFile.WriteLine "        <PasswordExpires>" & objItem.PasswordExpires & "</PasswordExpires>"
      objFile.WriteLine "        <PasswordRequired>" & objItem.PasswordRequired & "</PasswordRequired>"
      objFile.WriteLine "        <SID>" & objItem.SID & "</SID>"
      objFile.WriteLine "        <SIDType>" & objItem.SIDType & "</SIDType>"
      objFile.WriteLine "        <Status>" & objItem.Status & "</Status>"
      objFile.WriteLine "      </user>"
   Next
   objFile.WriteLine "    </local_usr>"
End Sub

Sub QFE_Info
On Error Resume Next
   Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\CIMV2")
   Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_QuickFixEngineering",,48)
   objFile.WriteLine "    <qfe_info>"
   For Each objItem In colItems
      objFile.WriteLine "      <fix>"
      objFile.WriteLine "        <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "        <CSName>" & objItem.CSName & "</CSName>"
      objFile.WriteLine "        <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "        <FixComments>" & objItem.FixComments & "</FixComments>"
      objFile.WriteLine "        <HotFixID>" & objItem.HotFixID & "</HotFixID>"
      objFile.WriteLine "        <InstalledBy>" & objItem.InstalledBy & "</InstalledBy>"
      objFile.WriteLine "        <InstalledOn>" & objItem.InstalledOn & "</InstalledOn>"
      objFile.WriteLine "        <Name>" & objItem.Name & "</Name>"
      objFile.WriteLine "        <ServicePackInEffect>" & objItem.ServicePackInEffect & "</ServicePackInEffect>"
      objFile.WriteLine "        <Status>" & objItem.Status & "</Status>"
      objFile.WriteLine "      </fix>"
   Next
   objFile.WriteLine "    </qfe_info>"
End Sub

Sub Prog_Info
On Error Resume Next
   Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\CIMV2")
   Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_Product",,48)
   objFile.WriteLine "    <products>"
   For Each objItem In colItems
      objFile.WriteLine "      <program>"
      objFile.WriteLine "        <Caption>" & objItem.Caption & "</Caption>"
      objFile.WriteLine "        <Description>" & objItem.Description & "</Description>"
      objFile.WriteLine "        <IdentifyingNumber>" & objItem.IdentifyingNumber & "</IdentifyingNumber>"
      objFile.WriteLine "        <InstallDate>" & objItem.InstallDate & "</InstallDate>"
      objFile.WriteLine "        <InstallLocation>" & objItem.InstallLocation & "</InstallLocation>"
      objFile.WriteLine "        <InstallState>" & objItem.InstallState & "</InstallState>"
      objFile.WriteLine "        <Name>" & objItem.Name & "</Name>"
      objFile.WriteLine "        <PackageCache>" & objItem.PackageCache & "</PackageCache>"
      objFile.WriteLine "        <SKUNumber>" & objItem.SKUNumber & "</SKUNumber>"
      objFile.WriteLine "        <Vendor>" & objItem.Vendor & "</Vendor>"
      objFile.WriteLine "        <Version>" & objItem.Version & "</Version>"
      objFile.WriteLine "      </program>"
   Next
   objFile.WriteLine "    </products>"
End Sub

objFile.WriteLine "  <!-- Script-EOF:" & DateDiff("s", "12/31/1970 00:00:00", Now) & " -->"
objFile.WriteLine "</Inventory>"
Set objFile = Nothing
Set objFileSystem = Nothing