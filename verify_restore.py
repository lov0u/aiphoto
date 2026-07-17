import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Quick check: does the CSS now contain gen-page styles?
stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page\\|gen-layout\\|gen-panel" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("gen-page style rules:", stdout.read().decode().strip())

# Check home page loads
stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/')
print("\nHome page HTTP code:", stdout.read().decode().strip())

# Check generate page loads
stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate')
print("Generate page HTTP code:", stdout.read().decode().strip())

ssh.close()
