<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* ���������� ������� */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."forums`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* ���� ����� ������� ������ ���������� ��������� - �������� ������� ��������� ��� */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* ��� ��� ������� ��������������� ��������� ��� � ������ ��������� � ���������� �����(���� �� ����������) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{

	$query = "SELECT * FROM ".$arResult["prefix"]."forums LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	$res = CForumGroup::GetList(array(), array("SORT" => 777))->GetNext();
	
	while($arItem = mysql_fetch_assoc($result))
	{
		if(CForumNew::GetList(array(), array("XML_ID" => $arItem["forum_id"]))->GetNext()) continue;
		$arFields = Array(
		   "XML_ID" => $arItem["forum_id"],
		   "ACTIVE" => "Y",
		   "NAME" => $arItem["forum_name"],
		   "DESCRIPTION" => $arItem["forum_desc"],
		   "FORUM_GROUP_ID" => $res["ID"],
		   "GROUP_ID" => array(1 => "Y"), 
		   "SITES" => array(
		       $wizard->GetVar("siteID") => "/url/"));
		$ID = CForumNew::Add($arFields);		
	}



	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;
}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
