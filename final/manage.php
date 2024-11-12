<?php include('userconfig/constants.php');
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch foods based on search query
$sql = "SELECT * FROM foods WHERE status = 1 AND food_name LIKE '%$search%'";
$res = mysqli_query($conn, $sql);
$count = mysqli_num_rows($res);


if($_SERVER["REQUEST_METHOD"]=="POST")
{
    if(isset($_POST['add_to_order']))
    {
        if(isset($_SESSION['OrderCart']))
        {
            $myitems = array_column($_SESSION['OrderCart'],'Item_Name');
            if(in_array($_POST['Item_Name'], $myitems))
            {
                
         //       echo "<script>
         //       alert('Item Already In Cart'); 
          //      window.location.href='index.php';
          //      </script>"; 
                 
            }
            else
            {

            
            $count = count($_SESSION['OrderCart']); 
            $_SESSION['OrderCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
        //    echo "<script>
                
          //      window.location.href='index.php';
          ///      </script>";
                
            }
            

        }
        else
        {
            $_SESSION['OrderCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
         //   echo "<script>
              
         //       window.location.href='index.php';
          //      </script>";
                
        }

    }
    if(isset($_POST['Remove_Item']))
    {
        foreach($_SESSION['OrderCart'] as $key => $value)
        {
            if($value['Item_Name']==$_POST['Item_Name'])
            {
            unset($_SESSION['OrderCart'][$key]);
            $_SESSION['OrderCart']=array_values($_SESSION['OrderCart']);
            echo "<script>
           
            window.location.href='orderlist.php';
            
            </script>";
            
            }
        }
    }
    if(isset($_POST['Mod_Quantity']))
    {
        foreach($_SESSION['OrderCart'] as $key => $value)
        {
            if($value['Item_Name']==$_POST['Item_Name'])
            {
                $_SESSION['OrderCart'][$key]['Quantity']=$_POST['Mod_Quantity'];
           
            echo "<script>
          
            window.location.href='orderlist.php';
            </script>";
           
            }
        } 
    }
    
}

?>