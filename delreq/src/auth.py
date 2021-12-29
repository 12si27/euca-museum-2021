from bs4 import BeautifulSoup
import requests
import random, time, sys, pymysql

header = {
    'Referer':'https://www.dcinside.com/',
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.113 Safari/537.36'
}

url = 'https://dcid.dcinside.com/join/member_check.php'


try:

    soup = BeautifulSoup(requests.get("https://sign.dcinside.com/login").text, features='html.parser')
    loginForm = soup.find('form')

    auth = loginForm.find('input', attrs={'name':'ci_t'})['value']

    data = {
        'ci_t': auth,
        'pw':'',
        'user_id':'',
        's_url':'https%3A%2F%2Fgall.dcinside.com%2F',
        'ssl':'Y'
    }

    target_id = sys.argv[1]

    time.sleep(2)

    session = requests.session()
    s = session.post(url,headers=header,data=data)
    gallog_url = 'https://gallog.dcinside.com/' + target_id + '/guestbook'


    time.sleep(2)

    gpage = session.get(gallog_url, headers=header)
    soup = BeautifulSoup(gpage.text, features='html.parser')
    cmts = soup.find_all('ul', class_='user_data_list')

    already_exists = False

    for c in cmts:
        # 이미 갤로그에 남겨져 있다면
        if (c.find('a')):
            if (c.a['href'] == '/127bot2'):
                already_exists = True
                break

    if already_exists:
        state = 'EXIST'

    elif (gpage.status_code != 200):
        state = 'ERROR'

    else:

        time.sleep(1)

        randcode = ''.join(random.choice(['A','B','C','D','E','F','G','H','K','1','2','3','4','5','6','7','8','9']) for _ in range(6))

        gdata = {
            'ci_t': gpage.cookies['ci_c'],
            'memo': '애유갤 박물관 인증 코드: ' + randcode,
            'is_secret': '1'
        }

        gheader = {
            'Connection': 'keep-alive',
            'Accept': 'application/json, text/javascript, */*; q=0.01',
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.113 Safari/537.36',
            'Origin': 'https://gallog.dcinside.com',
            'Referer': 'https://gallog.dcinside.com/' + target_id + '/guestbook',
        }

        s2 = session.post('https://gallog.dcinside.com/' + target_id + '/ajax/guestbook_ajax/write',headers=gheader, data=gdata)

        conn = pymysql.connect(
            user='', 
            passwd='', 
            host='', 
            db='', 
            charset=''
        )

        cursor = conn.cursor(pymysql.cursors.DictCursor)

        sql = "INSERT INTO `euca_museum_auth` (`id`, `code`) values ('" + target_id + "', '" + randcode + "') ON DUPLICATE KEY UPDATE `code` = '" + randcode + "', `date` = NOW()"
        cursor.execute(sql)
        conn.commit()
        conn.close()

        state = 'OK'

    session.close()
    
except:
    state = 'ERROR'

print('<state>'+state+'</state>')