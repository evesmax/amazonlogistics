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

    cursor.execute("SELECT * FROM logistica_recepciones WHERE idrecepcion IN (5823, 5842)")
    for r in cursor.fetchall():
        print(f"RECEPTION {r['idrecepcion']}:")
        print(r)
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
