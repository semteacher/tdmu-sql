 <script type="text/javascript" src="../testcenter/datepicker/jquery.js"></script>
 
<?php  
include "class/function.php";
include "auth.php";
if ($_SESSION['name_sesion_a']=="admin"){
include "menu.php";
include "navigate.php";
require_once "class/class_firebird.php";
require_once "class/mysql_class_local.php";
require_once "class/mysql_class_moodle2.php";
$contingent = new class_ibase();
$base_local = new class_mysql_base_local();
$base_moodle = new class_mysql_base_moodle();
$department = $contingent->select("select DISTINCT GUIDE_DEPARTMENT.DEPARTMENTID,GUIDE_DEPARTMENT.DEPARTMENT from  B_TESTLIST, GUIDE_DEPARTMENT WHERE B_TESTLIST.DEPARTMENTID=GUIDE_DEPARTMENT.DEPARTMENTID ORDER by GUIDE_DEPARTMENT.DEPARTMENTID DESC;");
echo "<center><h2>��� ���������� � ���� ����������
<table width=100%><tr><td width=70% valign=top>";
navigate('���������',$department,'DEPARTMENT');
if ($_GET['DEPARTMENT'])
	{
$speciality = $contingent->select("select DISTINCT BVI_V.SPECIALITYID,GS.CODE|| '-' ||GS.SPECIALITY from  B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join B_VARIANT_ITEMS BVI_V   on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) inner join GUIDE_SPECIALITY GS on (BVI_V.SPECIALITYID = GS.SPECIALITYID) where  BT.DEPARTMENTID = ".$_GET['DEPARTMENT']."");
		navigate('�������������',$speciality,'SPECIALITY');	
	}
if ($_GET['SPECIALITY'])
	{
	$discipline = $contingent->select("select DISTINCT gd.disciplineid,gd.discipline from  B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) inner join GUIDE_SPECIALITY GS on (BVI_V.SPECIALITYID = GS.SPECIALITYID) inner join guide_discipline gd on (BVI_V.disciplineid = gd.disciplineid) where  BT.DEPARTMENTID = ".$_GET['DEPARTMENT']." AND BVI_V.SPECIALITYID = ".$_GET['SPECIALITY']." order by gd.discipline");
		navigate('���������',$discipline,'DISCIPLINE');	
	}
	
if ($_GET['DISCIPLINE'])
	{	
$ekzam = array(array('������ 0'));
$year =$contingent->select("select DISTINCT GE.EDUYEARSTR,GE.EDUYEAR from B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) inner join GUIDE_EDUYEAR GE on (BT.EDUYEAR = GE.EDUYEAR) where BVI_V.DISCIPLINEID = ".$_GET['DISCIPLINE']." and BVI_V.SPECIALITYID = ".$_GET['SPECIALITY']." and BT.DEPARTMENTID = ".$_GET['DEPARTMENT']."");	
$sem =$contingent->select("select   DISTINCT BT.SEMESTER from B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) where BVI_V.DISCIPLINEID = ".$_GET['DISCIPLINE']." and BVI_V.SPECIALITYID = ".$_GET['SPECIALITY']." and BT.DEPARTMENTID = ".$_GET['DEPARTMENT']."");
  echo "</td><td bgcolor=gray valign=top><center> Գ����";
		echo "<br><br><form action='ser.php?".$_SERVER['QUERY_STRING']."' method='post' enctype='multipart/form-data'><table bgcolor='white'><tr><td valign=top>";
		check('г�',$year,'year');
		echo "</td><td valign=top>";
		check('��������',$sem,'sem');
		echo "</td><td valign=top>";
		check('�������',$ekzam,'ekzam');
		echo "</td></tr><tr><td></td><td><input type='submit' name='put' value='�������'></td></tr></table>";
 
 if($_POST['put']||$_POST['var'])
 {
	$year_v = array();
	$sem_v = array();
	$s=0;$y=0;
    for ($i=0;$i<count($year)+count($sem)+1;$i++)
    {
        if ($_POST["year".$i]) {$year_v[$y]=$year[$i][1];$y++;}
		if ($_POST["sem".$i]) {$sem_v[$s]=$sem[$i][0];$s++;}
    } 
$sem_sql=$contingent->for_sql($sem_v);	
$year_sql=$contingent->for_sql($year_v);	

if ($sem_sql!=''){
$sem_sql="AND BT.SEMESTER in (".$sem_sql.")";}
if ($year_sql!='')
{
$year_sql="and BT.EDUYEAR in (".$year_sql.")";
}
 }else{
$sem_sql='';
$year_sql='';
  }
 $zag_mas =$contingent->select("select DISTINCT BVI_V.VARIANTID,GE.EDUYEARSTR,BT.SEMESTER,bvm.modulenum,bvm.moduletheme from B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join b_variant_module bvm on (BVI_M.VARIANTID = bvm.VARIANTID) inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) inner join GUIDE_EDUYEAR GE on (BT.EDUYEAR = GE.EDUYEAR) where BVI_V.DISCIPLINEID = ".$_GET['DISCIPLINE']." and BVI_V.SPECIALITYID = ".$_GET['SPECIALITY']." and BT.DEPARTMENTID = ".$_GET['DEPARTMENT']." ".$sem_sql." ".$year_sql." order by BVI_V.VARIANTID,GE.EDUYEARSTR,BT.SEMESTER,bvm.modulenum"); 
  echo "</td></tr><tr><td colspan=2><br>";
$discipl=$base_local->select("SELECT DISTINCT id_kaf_moodle FROM discipline WHERE id_kontingent='".(int)$_GET['DISCIPLINE']."';");
$kaf=$base_moodle->select("SELECT id,name FROM mdl_course_categories WHERE id = '".(int)$discipl[0][0]."';");
$parts = explode("(", strip_tags($kaf[0][1]));

echo "select DISTINCT BVI_V.VARIANTID,GE.EDUYEARSTR,BT.SEMESTER,bvm.modulenum,bvm.moduletheme from B_TESTLIST BT inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) inner join b_variant_module bvm on (BVI_M.VARIANTID = bvm.VARIANTID) inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) inner join GUIDE_EDUYEAR GE on (BT.EDUYEAR = GE.EDUYEAR) where BVI_V.DISCIPLINEID = ".$_GET['DISCIPLINE']." and BVI_V.SPECIALITYID = ".$_GET['SPECIALITY']." and BT.DEPARTMENTID = ".$_GET['DEPARTMENT']." ".$sem_sql." ".$year_sql." order by BVI_V.VARIANTID,GE.EDUYEARSTR,BT.SEMESTER,bvm.modulenum";
	if ($parts[0]!='')
	{
		echo "<center style='background-color:white;'>������� (<font color=green >".$parts[0]."</font>)</center>";
	}
		else
	{
		echo "<center style='background-color:white;'><font color=red>�� ��������� �� ��'����� � ��������, ���������� �� �������������!</font></center>";
	}
	
  echo "
  <table bgcolor='white' border=1 width=100% class='ser2'><tr style='text-align:center' bgcolor=gray><td width=3%>
  <input type='checkbox' id='toggle' value='S' onClick='do_this()' /></td><td>����� �������</td><td>г�</td><td>��������</td><td>�����<br>������</td><td>����� ������</td>";

  for ($i=0;$i<count($zag_mas);$i++)
  {
	echo "<tr class='id2' name='id[".$zag_mas[$i][0]."]'><td bgcolor=gray><input type='checkbox' class='id' name='id[".$zag_mas[$i][0]."]'></td>";
		for($j=0;$j<count($zag_mas[0]);$j++)
		{
			echo "<td><center>".$zag_mas[$i][$j]."</td>";
		}
  echo "</tr>";
  }
echo "</table><br><center><input type='submit' name='var' value='�������'><br><br></form>";
if(($_POST['var'])&&(!empty($_POST['id'])))
 {
 $id= array();
 $i=0;
 if (!empty($_POST['id']))
 {
  $chb = $_POST['id'];
  foreach($chb as $index => $go)
   {
   $id[$i]=$index;
   $i++;
   };
 };
 $id_sql=$contingent->for_sql($id);	
 if ($_POST['ekzam0']=='on'){$exampl=" AND S2T.credits_test>10 ";}else{$exampl="";}
echo " <script type='text/javascript'> var mas = ".js_array($id)."</script>";
  $zagalne =$contingent->select("select avg( S2T.CREDITS_CUR ) avg_of_credits_cur, avg( S2T.credits_test ) avg_of_credits_test, avg( S2T.credits_all ) 
  avg_of_credits_all from STUDENT2TESTLIST S2T 
  inner join B_TESTLIST BT on (BT.TESTLISTID = S2T.TESTLISTID) 
  inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) 
  inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) 
  where BVI_V.VARIANTID in (".$id_sql.") ".$exampl.";");
 echo "<font color=red><b>select avg( S2T.CREDITS_CUR ) avg_of_credits_cur, avg( S2T.credits_test ) avg_of_credits_test, avg( S2T.credits_all ) 
  avg_of_credits_all from STUDENT2TESTLIST S2T 
  inner join B_TESTLIST BT on (BT.TESTLISTID = S2T.TESTLISTID) 
  inner join B_VARIANT_ITEMS BVI_M on (BVI_M.VARIANTID = BT.VARIANTID) 
  inner join B_VARIANT_ITEMS BVI_V on (BVI_V.VARIANTID = BVI_M.PARENTVARIANTID) 
  where BVI_V.VARIANTID in (".$id_sql.") ".$exampl.";</b></green>";
echo" <table bgcolor='white' border=1 width = 100% class='ser'><tr><td colspan=3>��������� �� �������� ��������� (".$id_sql.")</td></tr>
<tr><td><center><b>�������</td><td><center><b>�������������</td><td><center><b>��������</td></tr>";
  for ($i=0;$i<count($zagalne);$i++)
  {
  echo "<tr>";
		for($j=0;$j<count($zagalne[0]);$j++)
		{
			echo "<td><center>".$zagalne[$i][$j]."</td>";
		}
  echo "</tr>";
  }
echo "</table>";
 }
}}else {header("Location: index.php");}
?> 
<script type="text/javascript" src="script/ser.js"></script>