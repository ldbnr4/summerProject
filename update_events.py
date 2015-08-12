import requests

page = requests.get('http://vamonos-vamos.rhcloud.com/update')
print page.text

