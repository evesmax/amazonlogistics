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

    for trigger_name in ("trg_recepciones_insert", "trg_recepciones_update"):
        cursor.execute(f"SHOW CREATE TRIGGER {trigger_name}")
        trg = cursor.fetchone()
        if trg:
            print(f"Trigger {trigger_name} SQL:")
            print(trg['SQL Original Statement'])
            print("=" * 80)
        else:
            print(f"Trigger {trigger_name} not found.")

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
