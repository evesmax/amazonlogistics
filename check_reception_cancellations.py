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

    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (nombreproceso LIKE '%RECEPCION%' OR sqlproceso LIKE '%recepcion%')
          AND (nombreproceso LIKE '%CANCEL%' OR nombreproceso LIKE '%ELIMIN%' OR sqlproceso LIKE '%cancel%' OR sqlproceso LIKE '%eliminar%')
        ORDER BY fecha ASC
    """
    cursor.execute(sql)
    rows = cursor.fetchall()
    print(f"Found {len(rows)} reception cancellations:")
    for r in rows:
        print(f"FECHA: {r['fecha']} | PROCESO: {r['nombreproceso']}")
        print(f"SQL: {r['sqlproceso']}")
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
