function quotecomment(author,commentid,cbody,destination)
{
	cbody = cbody.replace(/§/g,"\n");
	//cbody = replace(/"<br />"/g,"\n");
	destination.value = "--------------------\n"+author+" went on record as saying:\n"+cbody+"\n--------------------\n";
	destination.focus();
}