import pymysql

DB_HOST = "34.66.63.218"
DB_USER = "nmdevel"
DB_PASS = "nmdevel"
DB_NAME = "_dbmlog0000018677"

def main():
    conn = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    print("--- SHOW TRIGGERS FOR inventarios_movimientos ---")
    cursor.execute("SHOW TRIGGERS LIKE 'inventarios_movimientos'")
    triggers = cursor.fetchall()
    print(f"Found {len(triggers)} triggers:")
    for t in triggers:
        print(f"Trigger: {t['Trigger']} | Event: {t['Event']} | Timing: {t['Timing']}")
        print(f"Statement: {t['Statement']}")
        print("-" * 80)
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
