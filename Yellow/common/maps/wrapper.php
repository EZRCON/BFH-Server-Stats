<?php
// BF4 Stats Page by Ty_ger07
// http://open-web-community.com/

// show loading...
echo '
<div id="loading">
<br/><br/>
<center><img class="load" src="./common/images/loading.gif" alt="loading" /></center>
<br/><br/>
</div>
';
// then ajax load content
echo '
<div id="loaded" style="display: none;">
<script type="text/javascript">
$(\'#loaded\').load("./common/maps/maps.php?gid=' . $GameID;
if(!empty($ServerID))
{
	echo '&sid=' . $ServerID;
}
if(!empty($page))
{
	echo '&p=' . $page;
}
echo '");
</script>
</div>
';
?>