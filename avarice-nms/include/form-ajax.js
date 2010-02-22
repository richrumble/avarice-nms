
function ButtonAutoSelect(str)
{
  str.checked=true;
}

function CheckButton(str)
{
  document.getElementById(str).checked=true;
}

function UncheckAllButton(str,str2)
{
  if (document.getElementById(str).checked==false)
  {
    document.getElementById(str2).checked=false;
  };
}

function ChangeButtonList(str,str2)
{
  var idarray=str2.split(",");
  for (var i in idarray)
  {
    if (document.getElementById(str).checked==true)
    {
      document.getElementById(idarray[i]).checked=true;
    }
    else
    {
      document.getElementById(idarray[i]).checked=false;
    }
  }
}

function ClearOnSelect(str,str2)
{
  if (document.getElementById(str).value==str2)
  {
    document.getElementById(str).value="";
  }
}

function displaySwap(str,str2)
{
  if (document.getElementById(str).checked==true)
  {
    document.getElementById(str2).style.display="block";
  }
  else
  {
    document.getElementById(str2).style.display="none";
  }
}

function displayBlock(str)
{
  document.getElementById(str).style.display="block";
}

function displayInline(str)
{
  document.getElementById(str).style.display="inline";
}

function displayNone(str)
{
  document.getElementById(str).style.display="none";
}