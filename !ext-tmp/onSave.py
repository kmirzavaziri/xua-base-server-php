import os
import argparse
import subprocess
import json
import re

def closingMatch(s):
    started = False
    balance = 0
    for i in range(len(s)):
        if s[i] == '(':
            balance -= 1
        elif s[i] == ')':
            balance += 1

        if started and balance == 0:
            return i

        if balance != 0:
            started = True

def callAction(className, action):
    result = subprocess.check_output(['docker', 'exec', '-i', 'myfarm_php_1', 'php', '/public_html/xuaEngineHelper.php', action, className])
    print(result)
    return json.loads(result)

def setClassParameters(className):
    result = callAction(className, 'getClassParameters')
    if result['status']:
        with open(args.fileName, 'r') as f:
            content = f.read()
        content = re.sub(r'(\/\*\*.*\*\/\s*)?((abstract\s*)?class.*\})', lambda m, c=result['result'] : c + m.group(2), content, 1, re.DOTALL)
        with open(args.fileName, 'w') as f:
            f.write(content)
        print('SUCCESS')
    else:
        raise Exception('false result from PHP Engine Helper.\n')

def setRelationInverseColumns(className):
    result = callAction(className, 'getRelationInverseColumns')
    if result['status']:
        for res in result['result']:
            new = res['new']
            name = res['name']
            target = os.path.join(os.getcwd(), 'public_html', res['target'].replace('\\', '/') + '.php')
            text = res['text'].replace('\n', '\n            ')

            with open(target, 'r') as f:
                content = f.read()

            if new:
                content = re.sub(r'(function\s+_fields.*\{.*\[.*\))(,?)(\s*\]\s*\)\s*;\s*\})', lambda m, c=text, n=name : m.group(1) + ",\n            '" + name + "' => " + c + ',' + m.group(3), content, 1, re.DOTALL)
            else:
                content = re.sub(r'([\'\"]' + name + '[\'\"]\s*=>\s*)(.*)', lambda m, c=text : m.group(1) + c + m.group(2)[closingMatch(m.group(2)) + 1:], content, 1, re.DOTALL)

            with open(target, 'w') as f:
                f.write(content)

        print('SUCCESS')
    else:
        raise Exception('false result from PHP Engine Helper.\n')

def setMethodExecuteVars(className):
    result = callAction(className, 'getMethodExecuteVars')
    if result['status']:
        with open(args.fileName, 'r') as f:
            content = f.read()
        content = re.sub(r'(protected\s+function\s+execute\([^)]*\)[^{]*\{\s*)([^\/]*(\/\*\*.*\*\/\s*)?)', lambda m, c=result['result'] : m.group(1) + c, content, 1, re.DOTALL)
        with open(args.fileName, 'w') as f:
            f.write(content)
        print('SUCCESS')
    else:
        raise Exception('false result from PHP Engine Helper.\n')

def setEntityConstants(className):
    result = callAction(className, 'getEntityConstants')
    if result['status']:
        with open(args.fileName, 'r') as f:
            content = f.read()
        content = re.sub(r'(class\s+\w+\s+extends\s+\w+\s*\{\s*)(.*?)(protected|private|public|static|final|function)', lambda m, c=result['result'] : m.group(1) + c +  m.group(3), content, 1, re.DOTALL)
        with open(args.fileName, 'w') as f:
            f.write(content)
        print('SUCCESS')
    else:
        raise Exception('false result from PHP Engine Helper.\n')

parser = argparse.ArgumentParser()
parser.add_argument('fileName', type=str)

args = parser.parse_args()

className = args.fileName[len(os.path.join(os.getcwd(), 'public_html')) + 1:].split('.')[0].replace('/', '\\')
print(className)
try:
    print('setClassParameters: ', end = '')
    setClassParameters(className)
except Exception as e:
    print(e)
try:
    print('setRelationInverseColumns: ', end = '')
    setRelationInverseColumns(className)
except Exception as e:
    print(e)
try:
    print('setMethodExecuteVars: ', end = '')
    setMethodExecuteVars(className)
except Exception as e:
    print(e)
# try:
#     print('setEntityConstants: ', end = '')
#     setEntityConstants(className)
# except Exception as e:
#     print(e)