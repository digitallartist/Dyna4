Dim fs
set fs = CreateObject ( "Scripting.FileSystemObject" )
Set WinScriptHost = CreateObject("WScript.Shell")


while not fs.FileExists ("C:\xampp\htdocs\crons\stop.txt")
 Wscript.Sleep 36000
 call DoSomething
Wend

Sub DoSomething
	WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\dyno4\cron\get_currency_api_update.bat" & Chr(34), 0
	Wscript.Echo "...API0 " & now
end sub
