import socket

def db():
  hostname = socket.gethostname()
  if (hostname == 'DESKTOP-M0Q0TD7'):
    config = {
      'user': 'root',
      'password': '448545ct',
      'host': '143.54.112.91',
      'database': 'brapci_oaipmh',
      'charset': 'utf8'
    }
  else:
    config = {
      'user': 'root',
      'password': '448545ct',
      'host': '127.0.0.1',
      'database': 'brapci_oaipmh',
      'charset': 'utf8'
    }
  return config

def codec():
  return True