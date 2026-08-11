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
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND sqlproceso LIKE 'Insert Into logistica_recepciones%'
        ORDER BY fecha ASC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    
    out = []
    out.append("==================================================================")
    out.append(f"ALL RECEP INSERTIONS IN AUGUST 2026: {len(trans)}")
    out.append("==================================================================")
    for t in trans:
        out.append(f"- Logged at: {t['fecha']} | User: {t['usuario']} | IP: {t['ip']}")
        out.append(f"  SQL: {t['sqlproceso']}")
        out.append("-" * 80)

    with open("reception_insertions.txt", "w", encoding="utf-8") as f:
        f.write("\n".join(out))

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
