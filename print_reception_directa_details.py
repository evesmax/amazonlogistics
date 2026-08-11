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

    print("--- DETAILED TRANSACTION LOGS FOR TARGET RECEPCION DIRECTA ---")
    for tid in target_ids:
        sql = """
            SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
            FROM netwarelog_transacciones_2026_s2 
            WHERE nombreproceso = %s
            ORDER BY fecha ASC
        """
        cursor.execute(sql, [f"RECEPCION DIRECTA: {tid}"])
        trans = cursor.fetchall()
        print(f"================== RECEPCION DIRECTA: {tid} (Found {len(trans)}) ==================")
        for t in trans:
            print(f"- Logged at: {t['fecha']} | User: {t['usuario']} | IP: {t['ip']}")
            print(f"  SQL: {t['sqlproceso']}")
            print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
