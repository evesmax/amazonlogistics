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

    target_recs = (5763, 5772, 5790, 5791, 5819)
    target_shipments = (5759, 5768, 5786, 5787, 5815)

    print("--- CREATION LOGS FOR THE RECEPTIONS ---")
    for rec_id, env_id in zip(target_recs, target_shipments):
        sql = """
            SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
            FROM netwarelog_transacciones_2026_s2 
            WHERE sqlproceso LIKE %s
        """
        cursor.execute(sql, [f"%idenvio%, %{env_id}%"])
        trans = cursor.fetchall()
        print(f"For Reception {rec_id} (Shipment {env_id}), found {len(trans)} transactions:")
        for t in trans:
            if "logistica_recepciones" in t['sqlproceso']:
                print(f"- Date: {t['fecha']} | User: {t['usuario']} | Proceso: {t['nombreproceso']} | IP: {t['ip']}")
                print(f"  SQL: {t['sqlproceso']}")
                print("-" * 80)
        print("=" * 100)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
