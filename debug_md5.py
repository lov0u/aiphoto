import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check the actual CSS file content on server
print("=== Server page-styles.css first 30 lines ===")
stdin, stdout, stderr = ssh.exec_command('head -30 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print(stdout.read().decode())

print("\n=== Server page-styles.css last 20 lines ===")
stdin, stdout, stderr = ssh.exec_command('tail -20 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print(stdout.read().decode())

# Check MD5
print("\n=== MD5 of server files ===")
stdin, stdout, stderr = ssh.exec_command('md5sum /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("CSS: " + stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('md5sum /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/js/main.js')
print("JS: " + stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('md5sum /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/page-generate.php')
print("PHP: " + stdout.read().decode().strip())

# Now check the LOCAL files
import hashlib
files = [
    r'D:\claud code项目\aiphoto-theme\assets\css\page-styles.css',
    r'D:\claud code项目\aiphoto-theme\assets\js\main.js',
    r'D:\claud code项目\aiphoto-theme\page-generate.php',
]
print("\n=== MD5 of local files ===")
for f in files:
    with open(f, 'rb') as fh:
        md5 = hashlib.md5(fh.read()).hexdigest()
    print("%s: %s" % (f.split('\\')[-1], md5))

# Check if there's a CDN or caching layer
print("\n=== Check for caching plugins ===")
stdin, stdout, stderr = ssh.exec_command('ls /www/wwwroot/aiphoto/wp-content/plugins/ | head -20')
print(stdout.read().decode())

# Check if there's a minified CSS that overrides
print("\n=== Check for minified/combined CSS ===")
stdin, stdout, stderr = ssh.exec_command('find /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme -name "*.min.css" -o -name "combined*.css" -o -name "bundle*.css" 2>/dev/null')
print(stdout.read().decode())

# Check if there's a cache directory
print("\n=== Check theme cache ===")
stdin, stdout, stderr = ssh.exec_command('find /www/wwwroot/aiphoto -path "*/cache/*" -name "*.css" 2>/dev/null | head -10')
print(stdout.read().decode())

ssh.close()
