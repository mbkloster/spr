<?php

$starttime = microtime();

require("../include/include.php");

$output->subtitle = 'Algebra Equation-o-Matic';

$output->addl("<h2 class=\"section\">Algebra Equation-o-Matic</h2>",2);
$output->addl("<p>Now you can easily check your algebraic equations without having to enter them manually in your calculator or some otherwise painful process! HOORAY!!!</p>",2);

$output->addl("<form action=\"algebra\" method=\"get\">",2);

$output->addl("<table cellspacing=\"0\" style=\"width: 65%; margin: auto;\">",2);
$output->addl("<tr>",3);
$output->addl("<td style=\"width: 50%\" valign=\"top\" class=\"extrapadding\"><b>Equation(s):</b><br /><input type=\"text\" name=\"eq\" style=\"width: 80%\" maxlength=\"100\" value=\"" . htmlspecialchars($_GET['eq']) . "\" /><br /><span class=\"small\">Enter equations as you would write them on paper, using x^y for exponents. You may enter multiple equations separated by an equal sign (=).</span></td>",4);
$output->addl("<td style=\"width: 50%\" valign=\"top\" class=\"extrapadding\"><b>Variables:</b><br /><input type=\"text\" name=\"vars\" style=\"width: 75%\" maxlength=\"100\" value=\"" . htmlspecialchars($_GET['vars']) . "\" /><br /><span class=\"small\">Separate variables with spaces. eg: x=3 y=5.2</span></td>",4);
$output->addl("</tr>",3);
$output->addl("<tr>",3);
$output->addl("<td colspan=\"2\" class=\"centered\"><input type=\"submit\" class=\"button\" value=\"Check Equation\" /> <input type=\"reset\" class=\"button\" value=\"Revert Fields\" /></td>",4);
$output->addl("</tr>",3);
$output->addl("</table>",2);

$output->addl("</form>",2);

if (trim($_GET['eq']) != '' && isset($_GET['vars']))
{
	// Process the equation
	$bracketfind = array('[',']','{','}','<','>','²','³',' ');
	$bracketreplace = array('(',')','(',')','(',')','^2','^3','');
	$_GET['eq'] = str_replace($bracketfind,$bracketreplace,$_GET['eq']);
	$_GET['vars'] = str_replace(" = ","=",$_GET['vars']);
	if (substr_count($_GET['eq'],'(') == substr_count($_GET['eq'],')'))
	{
		$equations = explode("=",$_GET['eq']);
		$variables = explode(" ",$_GET['vars']);
		for ($i = 0; $i < count($variables); $i++)
		{
			$currentvar = explode("=",$variables[$i]);
			if ($currentvar[0] != '' && count($currentvar) >= 2)
			{
				if ($currentvar[1] == '')
				{
					$currentvar[1] = 0;
				}
				else
				{
					if (strpos($currentvar[1],'/') !== false)
					{
						// variable is a fraction, leave it
					}
					elseif (strpos($currentvar[1],'.') === false)
					{
						settype($currentvar[1],'int');
					}
					else
					{
						settype($currentvar[1],'float');
					}
				}
				
				for ($x = 0; $x < count($equations); $x++)
				{
					$equations[$x] = str_complete_replace($currentvar[0].$currentvar[0],$currentvar[0].'*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'1',$currentvar[0].'*1',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'2',$currentvar[0].'*2',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'3',$currentvar[0].'*3',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'4',$currentvar[0].'*4',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'5',$currentvar[0].'*5',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'6',$currentvar[0].'*6',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'7',$currentvar[0].'*7',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'8',$currentvar[0].'*8',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'9',$currentvar[0].'*9',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'0',$currentvar[0].'*0',$equations[$x]);
					$equations[$x] = str_replace('1'.$currentvar[0],'1*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('2'.$currentvar[0],'2*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('3'.$currentvar[0],'3*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('4'.$currentvar[0],'4*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('5'.$currentvar[0],'5*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('6'.$currentvar[0],'6*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('7'.$currentvar[0],'7*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('8'.$currentvar[0],'8*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('9'.$currentvar[0],'9*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace('0'.$currentvar[0],'0*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace(')'.$currentvar[0],')*'.$currentvar[0],$equations[$x]);
					$equations[$x] = str_replace($currentvar[0].'(',$currentvar[0].'*(',$equations[$x]);
					$equations[$x] = str_replace($currentvar[0],"(".$currentvar[1].")",$equations[$x]);
				}
			}
		}
		
		$results = array();
		
		for ($i = 0; $i < count($equations); $i++)
		{
			$finaleq = '';
			for ($x = 0; $x < strlen($equations[$i]); $x++)
			{
				$currentchar = substr($equations[$i],$x,1);
				if ($currentchar == ' ' || $currentchar == '1' || $currentchar == '2' || $currentchar == '3' || $currentchar == '4' || $currentchar == '5' || $currentchar == '6' || $currentchar == '7' || $currentchar == '8' || $currentchar == '9' || $currentchar == '0' || $currentchar == '+' || $currentchar == '-' || $currentchar == '*' || $currentchar == '/' || $currentchar == '^' || $currentchar == '.' || $currentchar == '(' || $currentchar == ')')
				{
					$finaleq .= $currentchar;
				}
				else
				{
					$finaleq .= '0';
				}
			}
			if (strlen($finaleq) > 1 && (substr($finaleq,0,1) == '*' || substr($finaleq,0,1) == '/' || substr($finaleq,0,1) == '^'))
			{
				$finaleq = '0'.$finaleq;
			}
			if (strlen($finaleq) > 1 && (substr($finaleq,-1) == '+' || substr($finaleq,-1) == '-' || substr($finaleq,-1) == '*' || substr($finaleq,-1) == '/' || substr($finaleq,-1) == '^'))
			{
				$finaleq = $finaleq.'0';
			}
			$finaleq = str_replace('1(','1*(',$finaleq);
			$finaleq = str_replace('2(','2*(',$finaleq);
			$finaleq = str_replace('3(','3*(',$finaleq);
			$finaleq = str_replace('4(','4*(',$finaleq);
			$finaleq = str_replace('5(','5*(',$finaleq);
			$finaleq = str_replace('6(','6*(',$finaleq);
			$finaleq = str_replace('7(','7*(',$finaleq);
			$finaleq = str_replace('8(','8*(',$finaleq);
			$finaleq = str_replace('9(','9*(',$finaleq);
			$finaleq = str_replace('0(','0*(',$finaleq);
			$finaleq = str_replace(')1',')*1',$finaleq);
			$finaleq = str_replace(')2',')*2',$finaleq);
			$finaleq = str_replace(')3',')*3',$finaleq);
			$finaleq = str_replace(')4',')*4',$finaleq);
			$finaleq = str_replace(')5',')*5',$finaleq);
			$finaleq = str_replace(')6',')*6',$finaleq);
			$finaleq = str_replace(')7',')*7',$finaleq);
			$finaleq = str_replace(')8',')*8',$finaleq);
			$finaleq = str_replace(')9',')*9',$finaleq);
			$finaleq = str_replace(')0',')*0',$finaleq);
			$finaleq = str_replace(')(',')*(',$finaleq);
			$finaleq = str_complete_replace('0+0','0',$finaleq);
			$finaleq = str_complete_replace(' 0*0*',' 0*',$finaleq);
			$finaleq = str_complete_replace(' 0*0)',' 0)',$finaleq);
			$finaleq = str_complete_replace(' 0*0 ',' 0 ',$finaleq);
			$finaleq = str_complete_replace('(0*0 ','(0 ',$finaleq);
			$finaleq = str_complete_replace('(0*0*','(0*',$finaleq);
			$finaleq = str_complete_replace('(0*0)','(0)',$finaleq);
			$finaleq = str_complete_replace('0-0','0',$finaleq);
			$expsearch = array('++','---','**','//','+*','+/','*/','/*','-*','-/');
			$expreplace = array('+','-','*','/','+','+','*','/','-','-');
			$finaleq = str_complete_replace($expsearch,$expreplace,$finaleq);
			$equations[$i] = $finaleq;
			// Now, parse out the powers:
			$finaleq = preg_replace("/(\d*\.?\d+)\^(-?\d+\.?\d*)/","(pow(\\1,\\2))",$finaleq);
			$finaleq = preg_replace("/\((.+)\)\^(-?\d+\.?\d*)/","(pow((\\1),\\2))",$finaleq);
			$finaleq = preg_replace("/\((.+)\)\^\((.+)\)/","(pow((\\1),(\\2)))",$finaleq);
			$finaleq = preg_replace("/(-?\d+\.?\d*)\^\((.+)\)/","(pow(\\1,(\\2)))",$finaleq);
			if (strpos($finaleq,'/(0)') !== false || strpos($finaleq,'/0 ') !== false || strpos($finaleq,'/0+') !== false || strpos($finaleq,'/0-') !== false || strpos($finaleq,'/0/') !== false || strpos($finaleq,'/0*') !== false || strpos($finaleq,'/0^') !== false || substr($finaleq,-2) == '/0' || substr($finaleq,0,2) == '/0')
			{
				$results[$i] = 'Undefined';
			}
			else
			{
				if (trim($finaleq) != '' && trim($finaleq) != '+' && trim($finaleq) != '-' && trim($finaleq) != '--' && trim($finaleq) != '*' && trim($finaleq) != '/' && trim($finaleq) != '^')
				{
					
					eval("\$results[$i] = @($finaleq);");
				}
				else
				{
					$results[$i] = 0;
				}
			}
		}
		if (count($equations) > 1)
		{
			$output->addl("<p>" . htmlspecialchars($_GET['eq']) . "</p>",2);
			$output->addl("<p style=\"padding-left: 5%;\">" . htmlspecialchars(implode(" = ",$equations)) . "</p>",2);
			if ($results[0] != 'Undefined')
			{
				$equal = 1; // Assume they're equal until proven otherwise
			}
			else
			{
				$equal = 0; // UNDEFINED! And therefore invalid.
			}
			for ($i = 1; $i < count($results); $i++)
			{
				if (($results[$i] > ($results[0]+0.000000001) || $results[$i] < ($results[0]-0.000000001)) && $results[$i] != $results[0])
				{
					$equal = 0;
				}
			}
			if ($equal)
			{
				$color = 'green';
			}
			else
			{
				$color = 'red';
			}
			$output->addl("<p style=\"padding-left: 10%; color: $color;\"><b>" . htmlspecialchars(implode(" = ",$results)) . "</b></p>",2);
		}
		else
		{
			$output->addl("<p>" . htmlspecialchars($_GET['eq']) . "</p>",2);
			$output->addl("<p style=\"padding-left: 5%;\">" . htmlspecialchars($equations[0]) . "</p>",2);
			if ($results[0] != 'Undefined')
			{
				$output->addl("<p style=\"padding-left: 10%;\"><b>" . htmlspecialchars($results[0]) . "</b></p>",2);
			}
			else
			{
				$output->addl("<p style=\"padding-left: 10%; color: red;\"><b>" . htmlspecialchars($results[0]) . "</b></p>",2);
			}
		}
	}
	else
	{
		$output->addl("<p><i><b>Error:</b> The amount of opening and closing brackets in your equation(s) do not match!</i></p>",2);
	}
}

$output->addl("<p><b><a href=\"/tools/\">More tools...</a></b></p>",2);

$output->display();

?>