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

    target_receptions = (5767, 5776, 5794, 5795, 5823)
    target_shipments = (5763, 5772, 5790, 5791, 5819)

    print("--- SEARCHING FOR UPDATES/DELETES ON RECEPTIONS ---")
    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%update%logistica_recepciones%' OR sqlproceso LIKE '%delete%logistica_recepciones%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    print(f"Found {len(trans)} updates/deletes:")
    for t in trans:
        sql_lower = t['sqlproceso'].lower()
        is_relevant = any(str(rid) in sql_lower for rid in target_receptions + target_shipments)
        if is_relevant:
            print(f"- Date: {t['fecha']} | User: {t['usuario']} | Proceso: {t['nombreproceso']} | IP: {t['ip']}")
            print(f"  SQL: {t['sqlproceso']}")
            print("-" * 80)

    print("\n--- SEARCHING FOR UPDATES/DELETES ON SHIPMENTS ---")
    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%update%logistica_envios%' OR sqlproceso LIKE '%delete%logistica_envios%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    print(f"Found {len(trans)} updates/deletes:")
    for t in trans:
        sql_lower = t['sqlproceso'].lower()
        is_relevant = any(str(rid) in sql_lower for rid in target_shipments)
        if is_relevant:
            print(f"- Date: {t['fecha']} | User: {t['usuario']} | Proceso: {t['nombreproceso']} | IP: {t['ip']}")
            print(f"  SQL: {t['sqlproceso']}")
            print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
