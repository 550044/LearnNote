select uid,star+(pos-7)*500+500 from mem_user_qxd where pos>7;

insert into exc_start_sql(sql_str) values("update mem_user_qxd set level=21,pos=0,star=32472 where uid=301696000000000009;");              
