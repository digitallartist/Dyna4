Dim fs
set fs = CreateObject ( "Scripting.FileSystemObject" )
Set WinScriptHost = CreateObject("WScript.Shell")


while not fs.FileExists ("C:\xampp\htdocs\crons\stop.txt")
 Wscript.Sleep 10000
 call DoSomething
Wend

Sub DoSomething
	WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\dyno4\cron\cycle_ltc.bat" & Chr(34), 0
	Wscript.Echo "...3 " & now 
end sub
