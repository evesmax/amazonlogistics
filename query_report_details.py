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

    print("--- REPOLOGRAM METADATA FOR REPORT 24 ---")
    cursor.execute("SELECT * FROM repolog_reportes WHERE idreporte = 24")
    for r in cursor.fetchall():
        print(r)
    print()

    # Get the SQL query of the report
    cursor.execute("SELECT * FROM repolog_consultas WHERE idreporte = 24")
    for r in cursor.fetchall():
        print(r)
    print()

    # Get columns metadata
    cursor.execute("SELECT * FROM repolog_columnas WHERE idreporte = 24")
    cols = cursor.fetchall()
    for c in cols:
        print(f"Col: {c['campo']} | Link: {c['link']}")
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
