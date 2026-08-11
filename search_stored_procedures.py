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

    print("--- SEARCHING FOR stored procedure calls cancelacion_recepciones ---")
    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%cancelacion_recepciones%' OR nombreproceso LIKE '%CANCELACION RECEPCION%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    print(f"Found {len(trans)} cancellations:")
    for t in trans:
        print(f"- Date: {t['fecha']} | User: {t['usuario']} | Proceso: {t['nombreproceso']} | IP: {t['ip']}")
        print(f"  SQL: {t['sqlproceso']}")
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
