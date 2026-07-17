import paramiko
host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("Server CSS gen-page count:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('grep -c "agnes-" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("Server CSS agnes- count:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | grep -c "gen-page"')
print("Generate page has gen-page HTML:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate')
print("Generate HTTP:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/')
print("Home HTTP:", stdout.read().decode().strip())

ssh.close()
