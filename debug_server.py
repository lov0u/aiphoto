import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check what web server is running
stdin, stdout, stderr = ssh.exec_command('ps aux | grep -E "nginx|apache|httpd|php-fpm" | grep -v grep')
print("=== Running web processes ===")
print(stdout.read().decode())

# Check nginx config for aiphoto site
stdin, stdout, stderr = ssh.exec_command('nginx -T 2>/dev/null | grep -A5 "server_name.*aiphoto\|listen.*80" || echo "no nginx conf found"')
print("=== Nginx config ===")
print(stdout.read().decode())

# Check apache config
stdin, stdout, stderr = ssh.exec_command('apachectl -S 2>/dev/null || echo "no apache"')
print("=== Apache vhosts ===")
print(stdout.read().decode())

# Check what port is listening
stdin, stdout, stderr = ssh.exec_command('ss -tlnp | grep ":80\|:443\|:8080"')
print("=== Listening ports ===")
print(stdout.read().decode())

# Check if php-fpm is running
stdin, stdout, stderr = ssh.exec_command('php-fpm --version 2>/dev/null || php-fpm7.4 --version 2>/dev/null || echo "checking php-fpm status"')
print("=== PHP-FPM ===")
print(stdout.read().decode())

# Check the actual document root for aiphoto domain
stdin, stdout, stderr = ssh.exec_command('cat /etc/nginx/conf.d/*.conf 2>/dev/null | grep -i "aiphoto\|root\s" | head -20 || cat /usr/local/nginx/conf/vhost/*.conf 2>/dev/null | grep -i "aiphoto\|root\s" | head -20 || echo "no nginx vhost found"')
print("=== Nginx vhost root ===")
print(stdout.read().decode())

# Try to access the site via localhost
stdin, stdout, stderr = ssh.exec_command('wget -qO- --spider http://localhost 2>&1 | head -5 || curl -sI http://localhost 2>&1 | head -10')
print("=== Localhost HTTP check ===")
print(stdout.read().decode())

ssh.close()
