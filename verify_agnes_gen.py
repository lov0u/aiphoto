import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# PHP lint
stdin, stdout, stderr = ssh.exec_command('php -l /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php 2>&1')
print("PHP lint:", stdout.read().decode().strip())

# Check generate page
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | grep -c "agnes-app"')
print("agnes-app in page:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | grep -c "gen-page"')
print("gen-page in page:", stdout.read().decode().strip())

# Check home page
stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/')
print("Home HTTP:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate')
print("Generate HTTP:", stdout.read().decode().strip())

ssh.close()
