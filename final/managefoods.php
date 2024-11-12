<?php
include('userconfig/constants.php');
$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    if(isset($_POST['add_to_order']))
 {
        if(isset($_SESSION['OrderCart']))
        {
            
           
            $myitems = array_column($_SESSION['OrderCart'],'Item_Name');
                if(in_array($_POST['Item_Name'], $myitems))
              {
                
                echo "<script>
                alert('Food Already In Order List'); 
               
                </script>"; 
                 
               }
               else
              {

            
            $count = count($_SESSION['OrderCart']); 
            $_SESSION['OrderCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
                 window.location.href='foods.php';
                </script>";
                
               }
            

        }
        else
        {
            $_SESSION['OrderCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
              
                window.location.href='foods.php';
                </script>";
                
        }

    }elseif(isset($_POST['add_to_pre']))
    {
        if(isset($_SESSION['PreCart']))
        {
            
           
            $myitems = array_column($_SESSION['PreCart'],'Item_Name');
            if(in_array($_POST['Item_Name'], $myitems))
            {
                
                echo "<script>
                alert('Food Already In Pre-order List'); 
               
                </script>"; 
                 
            }
            else
            {

            
            $count = count($_SESSION['PreCart']); 
            $_SESSION['PreCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
                     window.location.href='foods.php';
                
                </script>";
                
            }
            

        }
        else
        {
            $_SESSION['PreCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
                window.location.href='foods.php';
                </script>";
                
        }
     
        
    }
    
}