import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check CSS file
stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page\\|gen-layout\\|gen-panel" %s/assets/css/page-styles.css' % remote_theme)
print('Old classes in CSS:', stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('grep -c "agnes-" %s/assets/css/page-styles.css' % remote_theme)
print('Agnes classes in CSS:', stdout.read().decode().strip())

# Show CSS structure - find where old and new sections are
stdin, stdout, stderr = ssh.exec_command('grep -n "^\\.gen-page\\|^\\.gen-layout\\|^\\.gen-panel\\|^\\.agnes\\|^/\\*\\|\\*\\|^/\\*\\*\\|^\\*\\|^\\*\\*\\|^/\\*$" %s/assets/css/page-styles.css | head -40' % remote_theme)
print('\n=== CSS section markers ===')
print(stdout.read().decode())

# Show the actual CSS content
stdin, stdout, stderr = ssh.exec_command('cat %s/assets/css/page-styles.css' % remote_theme)
css = stdout.read().decode()
print('\n=== Full CSS (first 100 lines) ===')
for i, line in enumerate(css.split('\n')[:100]):
    print('%3d: %s' % (i+1, line[:100]))

ssh.close()
