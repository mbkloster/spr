/*
	Senseless Political Ramblings: JS Form Functions
	Misc. functions for forms...
	Copyright © 2006 Senseless Political Ramblings
*/

function field_len_check(field,fieldtitle,minlen,maxlen)
{
	// Checks a field length
	// If you want to specify an indescriminate max length, use 0
	if ((field.value.length > maxlen && maxlen != 0) || field.value.length < minlen)
	{
		if (maxlen > 0)
		{
			if (minlen == 1)
			{
				alert(fieldtitle+" must be filled in, and no more than "+maxlen+" characters long.\nPlease correct this and try again.");
			}
			else
			{
				alert(fieldtitle+" must be between "+minlen+" and "+maxlen+" characters long.\nPlease correct this and try again.");
			}
		}
		else
		{
			if (minlen == 1)
			{
				alert(fieldtitle+" must be filled in.\nPlease correct this and try again.");
			}
			else
			{
				alert(fieldtitle+" must be at least "+minlen+" characters long.\nPlease correct this and try again.");
			}
		}
		field.focus();
		return false;
	}
	else
	{
		return true;
	}
}
