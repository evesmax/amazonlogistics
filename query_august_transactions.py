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

    target_ids = (5763, 5772, 5790, 5791, 5819)

    print("--- 1. TRANSACTIONS CONTAINING THE SPECIFIC FOLIO IDs ---")
    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%5763%' OR sqlproceso LIKE '%5772%' OR sqlproceso LIKE '%5790%' OR sqlproceso LIKE '%5791%' OR sqlproceso LIKE '%5819%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    print(f"Found {len(trans)} transactions:")
    for t in trans:
        print(f"- {t['fecha']} | {t['usuario']} | {t['nombreproceso']} | IP: {t['ip']}")
        print(f"  SQL: {t['sqlproceso']}")
        print("-" * 50)
    print()

    print("--- 2. ANY DELETE OR CANCEL STATEMENTS IN AUGUST 2026 ---")
    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%delete%' OR sqlproceso LIKE '%cancel%' OR sqlproceso LIKE '%idestadodocumento%' OR nombreproceso LIKE '%delete%' OR nombreproceso LIKE '%cancel%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    print(f"Found {len(trans)} delete/cancel/status-change transactions:")
    for t in trans:
        print(f"- {t['fecha']} | {t['usuario']} | {t['nombreproceso']} | IP: {t['ip']}")
        print(f"  SQL: {t['sqlproceso']}")
        print("-" * 50)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
