import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Get the home page output
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/ 2>&1 | head -150')
content = stdout.read().decode()
for i, line in enumerate(content.split('\n')[:150]):
    print("%3d: %s" % (i+1, line[:120]))

ssh.close()
