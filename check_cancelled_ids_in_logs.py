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

    target_ids = (5767, 5776, 5794, 5795, 5823)
    print("--- SEARCHING FOR TARGET CANCELLED RECEPTION IDs IN LOGS ---")
    sql = f"""
        SELECT fecha, usuario, nombreproceso, sqlproceso 
        FROM netwarelog_transacciones_2026_s2 
        WHERE sqlproceso LIKE '%%5767%%' 
           OR sqlproceso LIKE '%%5776%%'
           OR sqlproceso LIKE '%%5794%%'
           OR sqlproceso LIKE '%%5795%%'
           OR sqlproceso LIKE '%%5823%%'
           OR nombreproceso LIKE '%%5767%%'
           OR nombreproceso LIKE '%%5776%%'
           OR nombreproceso LIKE '%%5794%%'
           OR nombreproceso LIKE '%%5795%%'
           OR nombreproceso LIKE '%%5823%%'
    """
    cursor.execute(sql)
    rows = cursor.fetchall()
    print(f"Found {len(rows)} matching transactions:")
    for r in rows:
        print(f"FECHA: {r['fecha']} | PROCESO: {r['nombreproceso']}")
        print(f"SQL: {r['sqlproceso']}")
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
