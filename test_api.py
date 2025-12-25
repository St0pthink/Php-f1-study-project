import requests
import json

BASE_URL = "http://localhost:8000/api"
API_TOKEN = "6|tkhbY5FpGuuTGJvAdtqwbGopbjH5pgWdrrKfPxKz"

headers = {
    "Authorization": f"Bearer {API_TOKEN}",
    "Accept": "application/json",
    "Content-Type": "application/json"
}

def test_auth():
    print("Проверка авторизации...")
    r = requests.get(f"{BASE_URL}/drivers", headers=headers)
    if r.status_code == 200:
        print("Статус: 200 OK\n")
        return True
    print(f"Ошибка: {r.status_code}\n")
    return False

def test_get_drivers():
    print("GET /api/drivers")
    r = requests.get(f"{BASE_URL}/drivers", headers=headers)
    print(f"Статус: {r.status_code}")
    if r.status_code == 200:
        data = r.json()
        items = data.get('data', [])
        print(f"Получено: {len(items)} записей")
        if items:
            item = items[0]
            print(f"  id: {item.get('id')}")
            print(f"  title: {item.get('title')}")
            print(f"  is_friend: {item.get('is_friend')}")
        print()
        return items[0]['id'] if items else None
    return None

def test_get_driver(driver_id):
    print(f"GET /api/drivers/{driver_id}")
    r = requests.get(f"{BASE_URL}/drivers/{driver_id}", headers=headers)
    print(f"Статус: {r.status_code}")
    if r.status_code == 200:
        item = r.json()['data']
        print(f"  id: {item.get('id')}")
        print(f"  title: {item.get('title')}")
        print(f"  user: {item.get('user')}")
        print(f"  is_friend: {item.get('is_friend')}")
    print()

def test_post_driver():
    print("POST /api/drivers")
    payload = {
        "title": "API Test Driver",
        "track_name": "Test Track",
        "short_description": "Создано через API",
        "details_html": "<p>Детали</p>"
    }
    r = requests.post(f"{BASE_URL}/drivers", headers=headers, json=payload)
    print(f"Статус: {r.status_code}")
    if r.status_code == 201:
        item = r.json()['data']
        print(f"  id: {item.get('id')}")
        print(f"  title: {item.get('title')}")
        print(f"  user_id: {item.get('user_id')}")
        print(f"  is_friend: {item.get('is_friend')}")
        print()
        return item['id']
    print(f"  {r.text}\n")
    return None

def test_put_driver(driver_id):
    print(f"PUT /api/drivers/{driver_id}")
    payload = {"title": "API Test Driver (updated)"}
    r = requests.put(f"{BASE_URL}/drivers/{driver_id}", headers=headers, json=payload)
    print(f"Статус: {r.status_code}")
    if r.status_code == 200:
        item = r.json()['data']
        print(f"  id: {item.get('id')}")
        print(f"  title: {item.get('title')}")
        print(f"  is_friend: {item.get('is_friend')}")
    print()

def test_get_comments():
    print("GET /api/comments")
    r = requests.get(f"{BASE_URL}/comments", headers=headers)
    print(f"Статус: {r.status_code}")
    if r.status_code == 200:
        data = r.json()
        items = data.get('data', [])
        print(f"Получено: {len(items)} записей")
        if items:
            item = items[0]
            print(f"  id: {item.get('id')}")
            print(f"  body: {item.get('body')}")
            print(f"  is_friend: {item.get('is_friend')}")
            driver = item.get('driver', {})
            print(f"  driver.id: {driver.get('id')}")
            print(f"  driver.title: {driver.get('title')}")
            print(f"  driver.is_friend: {driver.get('is_friend')}")
    print()

def test_post_comment(driver_id):
    print("POST /api/comments")
    payload = {"driver_id": driver_id, "body": "Комментарий через API"}
    r = requests.post(f"{BASE_URL}/comments", headers=headers, json=payload)
    print(f"Статус: {r.status_code}")
    if r.status_code == 201:
        item = r.json()['data']
        print(f"  id: {item.get('id')}")
        print(f"  body: {item.get('body')}")
        print(f"  is_friend: {item.get('is_friend')}")
        driver = item.get('driver', {})
        print(f"  driver.id: {driver.get('id')}")
        print(f"  driver.title: {driver.get('title')}")
        print()
        return item['id']
    print(f"  {r.text}\n")
    return None

def test_put_comment(comment_id):
    print(f"PUT /api/comments/{comment_id}")
    payload = {"body": "Комментарий обновлен"}
    r = requests.put(f"{BASE_URL}/comments/{comment_id}", headers=headers, json=payload)
    print(f"Статус: {r.status_code}")
    if r.status_code == 200:
        item = r.json()['data']
        print(f"  id: {item.get('id')}")
        print(f"  body: {item.get('body')}")
        print(f"  is_friend: {item.get('is_friend')}")
        driver = item.get('driver', {})
        print(f"  driver.id: {driver.get('id')}")
        print(f"  driver.title: {driver.get('title')}")
    print()

def main():
    if not test_auth():
        return
    
    existing_id = test_get_drivers()
    if existing_id:
        test_get_driver(existing_id)
    
    driver_id = test_post_driver()
    if driver_id:
        test_put_driver(driver_id)
    
    test_get_comments()
    
    if driver_id:
        comment_id = test_post_comment(driver_id)
        if comment_id:
            test_put_comment(comment_id)

if __name__ == "__main__":
    main()
