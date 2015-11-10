

----------------------------------------------------------------------- 
Using Moodle customizable report features: 
WITHOUT ADDITIONAL GROUPING:
----------------------------------------------------------------------- 
SELECT cc.id "��� ���.", cc.name "�������", 
(CASE 
WHEN (locate('���', c.fullname) >0)AND(locate('���', c.fullname) >0) THEN "��������" 
WHEN (locate('������', c.fullname)>0)AND(locate('���', c.fullname) >0) THEN "��������������" 
WHEN (locate('����', c.fullname)>0)AND(locate('���', c.fullname) >0) THEN "��������������" 
WHEN (locate('����', c.fullname) >0)AND(locate('���', c.fullname) >0) THEN "�������������" 
WHEN (locate('����', c.fullname) >0)AND(locate('���', c.fullname) >0) THEN "�������������" 
WHEN (locate('���', c.fullname) >0)AND(locate('���', c.fullname) >0) THEN "�������������" 
WHEN (locate('���', c.fullname) >0)OR(locate('���', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('������', c.fullname) >0)OR(locate('���', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('����', c.fullname) >0)OR(locate('���', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('stomat', c.fullname) >0)OR(locate('eng', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('stom', c.fullname) >0)OR(locate('eng', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('med', c.fullname) >0)OR(locate('eng', c.fullname) >0) THEN "��������� ��������" 
WHEN (locate('farm', c.fullname) >0)OR(locate('eng', c.fullname) >0) THEN "��������� ��������" 
WHEN ((c.fullname like '%nurs%') >0)OR(locate('eng', c.fullname) >0) THEN "��������� ��������" 
END) AS faculty,
concat('<a target="_new" href="%%WWWROOT%%/course/view.php?id=',c.id,'">',c.fullname,'</a>') AS "�������", q1.name "��� �������", q1.filecount
FROM 
(SELECT mcm.course, mfl.name, count(mf.id) AS filecount
 FROM mdl_files mf 
 INNER JOIN mdl_context mctx ON mf.contextid = mctx.id 
 INNER JOIN mdl_course_modules mcm ON mctx.instanceid=mcm.id
 INNER JOIN mdl_folder mfl ON mcm.instance = mfl.id
 WHERE mcm.module=22 and mf.filesize>0 AND mcm.visible=1
GROUP BY mcm.course, mfl.name 
UNION ALL
SELECT mcm.course, mr.name, count(mr.id) AS filecount
FROM mdl_resource mr
INNER JOIN mdl_course_modules mcm ON (mr.id=mcm.instance AND mr.course=mcm.course)
WHERE mcm.visible=1
GROUP BY mcm.course, mr.name ) AS q1
LEFT JOIN mdl_course c ON q1.course = c.id 
LEFT JOIN mdl_course_categories cc ON cc.id=c.category
WHERE c.visible=1 
ORDER BY cc.id, faculty, q1.course, filecount DESC





SELECT LEFT(strip_tags(mcc.name),(POSITION((CHAR(0x28 USING utf8) COLLATE utf8_unicode_ci) IN strip_tags(mcc.name)))-1) as "�������", q1.cnt_bad "����������� ��ϲ/��ʲ", q2.cnt_good "³����� � ����������", round(100*q1.cnt_bad/q2.cnt_good,1) vidsotok 
FROM (
SELECT mc.category, count(ch.teachermark) cnt_bad FROM mdl_course mc JOIN mdl_checklist c ON c.course=mc.id JOIN mdl_checklist_item i ON c.id=i.checklist JOIN mdl_checklist_check ch ON i.id = ch.item WHERE (c.osceallowed=1)and(ch.teachermark=1)and(ch.oscemark=-1)and(ch.oscetimestamp >=UNIX_TIMESTAMP('2013-09-01 00:00:00')) group by mc.category
) q1 JOIN (
SELECT mc.category, count(ch.teachermark) cnt_good FROM mdl_course mc JOIN mdl_checklist c ON c.course=mc.id JOIN mdl_checklist_item i ON c.id=i.checklist JOIN mdl_checklist_check ch ON i.id = ch.item WHERE  (c.osceallowed=1)and(ch.teachermark=1)and(ch.oscetimestamp >= UNIX_TIMESTAMP('2013-09-01 00:00:00')) group by mc.category
) q2 on q1.category=q2.category LEFT JOIN mdl_course_categories mcc on q1.category=mcc.id ORDER BY mcc.name, vidsotok