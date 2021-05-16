import requests
import simplejson.errors as jerr

TEST_ENV_PORT = '8000'

response = requests.post('http://localhost:' + TEST_ENV_PORT + '/urpi/Methods/XUA/DeployTest')
if response.status_code != 200:
    raise Exception('Cannot test the project.')
try:
#     print(response.json()['errors'])
    print(response.json()['response']['alters'])
except jerr.JSONDecodeError:
    print('Expected valid JSON, got')
    print(response.content)
