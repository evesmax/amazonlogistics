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

    print("--- MENU/OPTIONS TABLES ---")
    cursor.execute("SHOW TABLES LIKE '%menu%'")
    for r in cursor.fetchall():
        print(r)
    print()

    # Let's search accelog_menu or similar
    try:
        cursor.execute("SELECT * FROM accelog_menu WHERE nombre LIKE '%Cancelar%' OR url LIKE '%cancel%'")
        for r in cursor.fetchall():
            print(r)
    except Exception as e:
        print("Error reading accelog_menu:", e)

    try:
        cursor.execute("SELECT * FROM accelog_opciones WHERE nombre LIKE '%Cancelar%' OR url LIKE '%cancel%'")
        for r in cursor.fetchall():
            print(r)
    except Exception as e:
        print("Error reading accelog_opciones:", e)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
