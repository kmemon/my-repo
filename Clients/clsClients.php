<?php

class clsClients
{
    public $aDataFields;
    
    function __construct()
    {
        $this->aDataFields = array("ShowLoginPage", "EmployeeLogin", "Dashboard", "EmployeesDirectory", "Files", "Calendar", "Discussions", "Inbox", "MainSearch", "School", "Recruitment", "Employees", "Payroll", "Trainings", "Reports", "Help");
    }
    
    function ClientsPages($sPage)
    {
        global $objGeneral;
        
        if ( $sPage == "" ) $sPage = "ClientsDashboard";
        switch($sPage)
        {
            case "ClientsDashboard": $sReturn = $this->ClientsDashboard(); break;
                
        }
        
        return($sReturn);
    }
function test()
{
	echo "bye";
}
    function newFUnction()
    {
        echo "<hi>";
        $a = "Changes to the wold"
    }
    
    function ClientsDashboard()
    {
        global $objDatabaseMaster;
        global $objAdminUI;
        global $aDatabases;
        

        $varResult2 = $objDatabaseMaster->Query("SELECT  C.DatabaseId,
        C.ClientId,
        C.ClientCode,
        C.ClientName,
        C.ClientURL,
        C.Status,
        C.Limits_DiskSpace FROM dbs AS D
        INNER JOIN clients AS C ON C.DatabaseId = D.DatabaseId
        WHERE C.Status = '1'
        ORDER BY C.ClientCode");
        $iTotalClient = $objDatabaseMaster->RowsNumber($varResult2);
        
        

        $sReturn ='
        <style>
        <!-- custom CSS -->
        <style>
        .width-30-pct{
            width:30%;
        }
        
        .text-align-center{
            text-align:center;
        }
        
        .margin-bottom-1em{
            margin-bottom:1em;
        }


        .float-button {
        font-size: 40px;
        position: fixed;
        bottom: 5%; /* Adjust height */
        right: 5%; /* Adjust position */
        z-index: 1000;
        }

        .float-button .height_fix {
        margin-top: 100%;
        border: 1px solid red;
        }

        .float-button .content {
        position: absolute;
        left: 0;
        top: 50%;
        height: 100%;
        width: 100%;
        text-align: center;
        margin-top: -20px;
        color: white;
        }/* Empty. Add your own CSS if you like */




       .city {display:none;}
        
        </style>


        
        <div class="content-wrapper">


        <div class="w3-container">
        <h3>Clients</h3>
      

        <div class="w3-row">
        <a href="javascript:void(0)" onclick="openCity(event, \'Dashboard\');">
        <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">Dashboard</div>
        </a>
        <a href="javascript:void(0)" onclick="openCity(event, \'ClientManagement\');">
        <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">Client Management</div>
        </a>
        <a href="javascript:void(0)" onclick="openCity(event, \'Tokyo\');">
        <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">Clients Detail</div>
        </a>
        </div>

        <div id="Dashboard" class="w3-container city">
            <div  class="w3-row-padding w3-margin-top">
        
        <!-- stratr !-->
        

        ';

        if($iTotalClient > 0)
        {


            for($z=0; $z< $iTotalClient; $z++)
            {
                $iStores =1;
                $sRawStore =0;
                $sSupervisor =0;
                $sMerchandiser =0;
                $sSkus =0;
                $iDatabaseId = $objDatabaseMaster->Result($varResult2, $z, "DatabaseId");
                $sClientName = $objDatabaseMaster->Result($varResult2, $z, "ClientName");




                $varResult_Stores = $aDatabases[$iDatabaseId]->Query("SELECT  S.ContactPersonName FROM stores AS S  INNER JOIN channels AS C   ON C.ChannelId = S.ChannelId  INNER JOIN towns AS T ON T.TownId = S.TownId ");
              if( $aDatabases[$iDatabaseId]->RowsNumber($varResult_Stores) > 0 )
              {
                  $iStores = $aDatabases[$iDatabaseId]->RowsNumber($varResult_Stores);
              }


                $varResult_Stores_Raw = $aDatabases[$iDatabaseId]->Query("SELECT S.ContactPersonMobileNumber  FROM stores_raw AS S  INNER JOIN channels AS C  ON C.ChannelId = S.ChannelId     INNER JOIN auditors AS A
                ON A.AuditorId = S.AuditorId  INNER JOIN stores_tagging_sub_locality AS SL  ON SL.SubLocalityId = S.SubLocalityId  INNER JOIN stores_tagging_locality AS L  ON L.LocalityId = SL.LocalityId  INNER JOIN stores_tagging_towns AS T  ON T.TownId = L.TownId " );

                if( $aDatabases[$iDatabaseId]->RowsNumber($varResult_Stores_Raw) > 0 )
                {
                    $sRawStore = $aDatabases[$iDatabaseId]->RowsNumber($varResult_Stores_Raw);
                }

                $varResult_SKU = $aDatabases[$iDatabaseId]->Query("SELECT  SB.BrandName FROM skus AS S  INNER JOIN skus_brands AS SB  ON SB.BrandId = S.BrandId INNER JOIN skus_categories AS SC  ON SC.CategoryId=S.CategoryId " );
                if( $aDatabases[$iDatabaseId]->RowsNumber($varResult_SKU) > 0 )
                {
                    $sSkus = $aDatabases[$iDatabaseId]->RowsNumber($varResult_SKU);
                }


                $varResult_SupervisorName = $aDatabases[$iDatabaseId]->Query("SELECT S.SupervisorName FROM supervisors AS S  " );

                if( $aDatabases[$iDatabaseId]->RowsNumber($varResult_SupervisorName) > 0 )
                {
                    $sSupervisor = $aDatabases[$iDatabaseId]->RowsNumber($varResult_SupervisorName);
                }
                $varResult_Auditors = $aDatabases[$iDatabaseId]->Query("SELECT  S.SupervisorName   FROM  auditors AS A  INNER JOIN supervisors AS S   ON S.SupervisorId = A.SupervisorId  " );
                if( $aDatabases[$iDatabaseId]->RowsNumber($varResult_Auditors) > 0 )
                {
                    $sMerchandiser = $aDatabases[$iDatabaseId]->RowsNumber($varResult_Auditors);
                }

               // $sReturn .= "CL: ".$sClientName."store: ".$iStores. "Rawst:".$sRawStore. "Sup:".$sSupervisor. "Merch:".$sMerchandiser. "Sku:".$sSkus."<br />";
                $sReturn .= $this->PieChart($sClientName, $iStores, $sRawStore, $sSupervisor, $sMerchandiser, $sSkus,$z);


              $sReturn .='<div id="demo"></div><div class="w3-card-2">
            <div class="box box-default">
            <div class="box-header with-border">
            <h3 class="box-title">'.$sClientName.'</h3>
            <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="row">
            <div class="col-md-8">
            <div class="chart-responsive">
            <canvas id="'.$sClientName.$z.'"  style="width: 200px; height:180px;"></canvas>
            </div>
            <!-- ./nmnmnchart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-md-4">
            <ul class="chart-legend clearfix">
            <li><i class="fa fa-circle-o text-red"></i> Products</li>
            <li><i class="fa fa-circle-o text-Blue"></i>Auditor</li>
            <li><i class="fa fa-circle-o text-aqua"></i>Raw Store</li>
            <li><i class="fa fa-circle-o text-light-blue"></i>Supervisor</li>
            <li><i class="fa fa-circle-o text-gray"></i>Stores</li>
            </ul>
            </div>
            <!-- /.col -->
            </div>
            <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
            <ul class="nav nav-pills nav-stacked">
            <li><a href="#">Supervisor <span class="pull-right text-red">'.$sSupervisor.'</span></a></li>
            <li><a href="#">Auditor <span class="pull-right text-green">'.$sMerchandiser.'</span></a></li>
            <li><a href="#">Store<span class="pull-right text-yellow">'.$iStores.'</span></a></li>
            <li><a href="#">Products<span class="pull-right text-yellow">'.$sSkus.'</span></a></li>
            <li><a href="#">Raw Store<span class="pull-right text-yellow">'.$sRawStore.'</span></a></li>
            </ul>
            </div>
            <!-- /.footer -->
            </div>
            </div>';





            }
        }
       






        $sReturn .='</div></div>
        </div>

        <div id="ClientManagement" class="w3-container city">
         
        <div class="box w3-middle" >
        <div class="box-header">
        <h3 class="box-title">Clients Detail</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
       
        <table id="myTable" class="mdl-data-table" cellspacing="0" width="100%" width="100%">
        <thead>
        <tr style="background: #2196f3;color: white;height: 40px;">
        <th class="width-30-pct">Client Code</th>
        <th class="width-30-pct">Client Name</th>
        <th class="width-30-pct">Client URL</th>
        <th class="width-30-pct">Disk Space in GBs</th>
        <th class="width-30-pct">Status</th>
        <th class="width-30-pct">Edit</th>
        
        </tr>
        </thead>
        <tbody>';
        
        
        if($iTotalClient > 0)
        {
            
            
            for($i = 0; $i < $iTotalClient; $i++)
            {
                
                $iClientId = $objDatabaseMaster->Result($varResult2, $i, "ClientId");
                $sClientCode = $objDatabaseMaster->Result($varResult2, $i, "ClientCode");
                $sClientName = $objDatabaseMaster->Result($varResult2, $i, "ClientName");
                $sClientURL = $objDatabaseMaster->Result($varResult2, $i, "ClientURL");
                $iStatus = $objDatabaseMaster->Result($varResult2, $i, "Status");
                $iDiskSpace = $objDatabaseMaster->Result($varResult2, $i, "Limits_DiskSpace");
                
                $sReturn .= '<tr>
                <td>'.$sClientCode.'</td>
                <td id="cname'.$sClientCode.'">'.$sClientName.'</td>
                <td><a href=http://'.$sClientURL.'>'.$sClientURL.'</a></td>
                <td id="diskspace'.$sClientCode.'">'.$iDiskSpace.'</td>
                <td>  <input type="checkbox" '.( ($iStatus == 1 )? 'checked': '' ).' class="minimal" data-toggle="toggle" data-on="Active" data-off="In active" onchange="ChangeStatus(this.id)" id="'.$sClientCode.'" cs="'.$iStatus.'"></td>
                <td><a class="mui-btn mui-btn--primary" id="'.$sClientCode.'" onClick="editModal(this.id);">Edit</a></td>
                </tr>';
                
                
            }
            
            
        }
        
        
        
        $sReturn .=' </tbody>
        
        </table>
        </div>
        <!-- /.box-body -->
        </div>

        <div class="float-button">
       
            <a class="w3-btn-floating-large w3-blue"  onClick="openModal();">+</a>
      
        </div>
        
        
        
        <div id="id01" class="w3-modal">

        <div class="w3-modal-content w3-card-8 w3-animate-zoom" style="max-width:600px">

        <div class="w3-center"><br>
        <span onclick="document.getElementById(\'id01\').style.display=\'none\'" class="w3-closebtn w3-hover-red w3-container w3-padding-8 w3-display-topright" title="Close Modal">×</span>
        <img src="" alt="Avatar" style="width:20%" class="w3-circle w3-margin-top" id="cimages">

        </div>

      <form class="w3-container" method="post" id="formdata">
        <div class="w3-section">
         <input type="hidden" name="mod" value="EditClient" id="mod">
          <input type="hidden" name="ccode" id="ccode">

          <label><b>Client Name</b></label>
          <input class="w3-input w3-border w3-margin-bottom" name="cn" type="text" id="cn" placeholder="Client Name"required>

          <label><b>Client Code</b></label>
          <input class="w3-input w3-border w3-margin-bottom" name="ccode1" type="text" id="ccode1"  placeholder="Client Code" required>

          <label><b>Disk Space Limit in GBs</b></label>
          <input class="w3-input w3-border w3-margin-bottom" name="dsp" type="number" id="dsp"  placeholder="Disk Space in GBs" required>

           <label><b>Logo</b></label>
          <input class="w3-input w3-border w3-margin-bottom" name="simage" type="file" id="simage">

          
          <img src="../images/whloading.gif" id="loader" style="display: none;padding-left: 50%;" />
         <button  class="w3-btn-block w3-green w3-section w3-padding" id="addClient" onclick="AddClient()">Register</button>
          <button class="w3-btn-block w3-green w3-section w3-padding" id="editClient" onclick="EditClient()">Update</button>
        
        </div>
      </form>

      

         </div>
        


        
        </div>




        </div>

        <div id="Tokyo" class="w3-container city">



         <div id="maindiv" style="margin-top: 20px;margin-left: 20px;">

         <div class="w3-row">
        <div class="col-sm-5">

        <select class="form-control" id="client" name="client">';
        for ($j=0; $j < $iTotalClient; $j++)
        {
            $iDataBaseId = $objDatabaseMaster->Result($varResult2, $j, "DatabaseId");
            $sClientName = $objDatabaseMaster->Result($varResult2, $j, "ClientName");
            $sReturn .= '<option value='.$iDataBaseId.' >'.$sClientName.'</option>';
        }

        $sReturn.='</select>
        </div>
        <div class="col-sm-5"> 
        <select class="form-control" id="discription" name="discription"  onChange="GetList();">
        <option value="stores">Stores</option>
        <option value="stores_raw">Raw Stores</option>
        <option value="skus">Product/SKUS</option>
        <option value="supervisors">Supervisor</option>
        <option value="auditors">Merchandiser/Auditors</option>
        </select>  
        </div>
        </div>

        <div id="showdata" style="margin-right: 20px; margin-bottom: 80px; top: 15px;position: relative;" ></div>

        </div>


         <div class="float-button">
       
            <a class="w3-btn-floating-large w3-green"  onClick="DwonloadCSV();"><i class="fa fa-download" aria-hidden="true"></i></a>
      
        </div>

       




        </div>
        
      
        </div>
        
        
        <script>
         GetList();

        openCity(event, \'Dashboard\');

       
        
        
        function  openModal()
        {
            document.getElementById(\'id01\').style.display=\'block\';
            $("#chead").html("Add New Client");
            $("#cn").val("");
            $("#ccode").val("");
            $("#ccode1").val("");
            $("#dsp").val("");
            $("#editClient").hide();
            $("#editClient").prop("disabled", true);
            $("#addClient").prop("disabled", false);
            $("#addClient").show();
            $("#ccode1").prop("disabled", false);
            $("#mod").val("AddClient");
            $("#loader").css("display","none");


             var src1 = "'.cDataFolder.'"  + "/NoPhoto.jpg";
             $("#cimages").attr("src", src1);

        }
        
        
        function  editModal(clientcode)
        {
            
            document.getElementById(\'id01\').style.display=\'block\';
            $("#chead").html("Edit Client");
            
            var cname = $("#cname" + clientcode).html();
            var dspace = $("#diskspace" + clientcode).html();

            var src1 = "'.cDataFolder.'" + "/" + clientcode + "/logo.png";
             $("#cimages").attr("src", src1);
            
            $("#cn").val(cname);
            $("#ccode1").val(clientcode);
            $("#ccode").val(clientcode);
            $("#dsp").val(dspace);
            $("#ccode1").prop("disabled", true);
            $("#editClient").show();
            $("#editClient").prop("disabled", false );
            $("#addClient").hide();
            $("#addClient").prop("disabled", true);
            $("#mod").val("EditClient");
           $("#loader").css("display","none");
            
        }
        
        
        function myFunction()
        {
            var input, filter, table, tr, td, i;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
        
        
        
        
        function ChangeStatus(sClientCode)
        {
            var iStatus = $("#"+ sClientCode).attr("cs");
            //alert(sClientCode + ">>" + iStatus);
            var sData = "mod=ChangeStatus&c=" + sClientCode + "&s=" + iStatus;
            var iActiveClients = parseInt($("#aClients").html());
            
            if ( iStatus == 1 )
                var iShowActiveClients = iActiveClients - 1;
            else
                var iShowActiveClients = iActiveClients + 1;
            
            swal({   title: "Are you sure?",   text: "You want to change the status!",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Yes",   cancelButtonText: "No",   closeOnConfirm: false,   closeOnCancel: false }, function(isConfirm){   if (isConfirm) {
                
                $.ajax({ type: "GET", url: "../pages/", data: sData,
                    success: function(res)
                    {
                        if (res == 0)
                            alert("Sorry, Status no updated!");
                        else
                        {
                            if ( iStatus == 1 ) iStatus = 0;
                            else if ( iStatus == 0 ) iStatus = 1;
                            
                            $("#"+ sClientCode).attr("cs", iStatus);
                            $("#aClients").html(iShowActiveClients);
                            swal("Yappiiee !", "Status changed!", "success");
                        }
                    }
                });
                return(false);
                
            } else {
                if ( iStatus == 1 )
                {
                    $("#"+ sClientCode).parent().removeClass();
                    $("#"+ sClientCode).parent().addClass( "toggle btn btn-primary" );
                }
                else if ( iStatus == 0 )
                {
                    $("#"+ sClientCode).parent().removeClass();
                    $("#"+ sClientCode).parent().addClass( "toggle btn btn-default off" );
                }
                swal("Cancelled", "Oooooops nothing happened :(", "error");
            return(false);  } });
        }
        
        function AddClient()
        {
        
       $("form#formdata").submit(function(event)
         {
            var sClientName = $("#cn").val();
            var sClientCode = $("#cc").val();

             var formData = new FormData($(this)[0]);

            
            if ( sClientName == "" )
            {
                swal("Warning !", "Please enter Client Name", "warning");
                return false;
            }
            if ( sClientCode == "" )
            {
                swal("Warning !", "Please enter Client Code", "warning");
                return false;
            }
            $("#addClient").css("display","none");
            $("#loader").css("display","block");
            
          /* $("#addClient").css("display","block");
            $("#loader").css("display","none");*/
          $.ajax(
            {
            url: "../pages/",
            type: "POST",
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (returndata) 
            {
                        
                        console.log(returndata);

                        if (returndata == 1)
                        {   
                            swal("Success !", "Client updated successfully", "success");
                             document.getElementById(\'id01\').style.display=\'none\';
                               
                        }
                        else if (returndata == 2)
                                    swal("Error !", "Error occured ! please contact to your manager", "error");
                }
//             ,
//              complete: function()
//              {
//                          $("#loader").css("display","none");
//              }
//             
             })
            
            
                  return false;
             });

         }


        function EditClient()
        {
         $("form#formdata").submit(function(event)
         {
            var sClientName = $("#cn").val();
            var sClientCode = $("#cc").val();

             var formData = new FormData($(this)[0]);

            
            if ( sClientName == "" )
            {
                swal("Warning !", "Please enter Client Name", "warning");
                return false;
            }
            if ( sClientCode == "" )
            {
                swal("Warning !", "Please enter Client Code", "warning");
                return false;
            }
            $.ajax({
            url: "../pages/",
            type: "POST",
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (returndata) {
                        
                        console.log(returndata);

                        if (returndata == 1)
                        {   

                           
                            swal("Success !", "Client updated successfully", "success");
                             document.getElementById(\'id01\').style.display=\'none\';
                               
                        }
                        else if (returndata == 2)
                                    swal("Error !", "Error occured ! please contact to your manager", "error");
                    }
                    });

                  return false;
             });

         }


          
        function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" w3-border-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.firstElementChild.className += " w3-border-red";
        }

        $(document).ready(function() {
        $("#myTable").DataTable( {
        columnDefs: [
        {
            targets: [ 0, 1, 2 ],
            className: "mdl-data-table__cell--non-numeric"
        }
        ]
        } );
        } );








        function GetList()
        {

        var tableName =$("#discription").val();

        var dataBaseId = $("#client").val();

        var sData = "mod=ClientDataList&db=" + dataBaseId + "&tn=" + tableName;

        $.ajax({ type: "GET", url: "../pages/", data: sData,
        success: function(data)
        {

        $("#showdata").html(data);

        }
        });


        }

        GetList();


        function DwonloadCSV()
        {

        var tableName =$("#discription").val();

        var dataBaseId = $("#client").val();

        var sData = "../pages/?mod=ClientDataList&db=" + dataBaseId + "&tn=" + tableName + "&csv=" + tableName + "CSV";
        //alert(sData);
        window.open(sData);


        }



        </script>';
        
        
        
        return($sReturn);
    }
    
    function AddClient()
    {
        global $objS3;
        global $objDatabaseMaster;
        global $objGeneral;


         $sClientName =  $objGeneral->fnGet("cn");
         $sClientCode =  $objGeneral->fnGet("ccode1");
         $sDiskSpace =  $objGeneral->fnGet("dsp");


        
        
        $dNow = $objGeneral->fnNow();
        $sHostName = "storevizcom.cfzawfiihbga.ap-southeast-1.rds.amazonaws.com";
        $sUserName = "storeviz1x2z2";
        $sPassword = "K2Az9xDgg2a";
        
        $varResult = $objDatabaseMaster->Query("SELECT ClientCode FROM clients WHERE ClientCode = '$sClientCode'");
        if ( $objDatabaseMaster->RowsNumber($varResult) > 0 )
            return 0;
        
        $varResult1 = $objDatabaseMaster->Query("SELECT AppVersion FROM clients ORDER BY ClientId DESC LIMIT 1");
        if ( $objDatabaseMaster->RowsNumber($varResult1) > 0 )
        {
            $dAppVersion = $objDatabaseMaster->Result($varResult1, 0, "AppVersion");
            $sClientURL = $sClientCode.".storeviz.com";
            //$sClientURL = "localhost";
            
            $iClientStartingYear = date("Y");
            $varResult2 = $objDatabaseMaster->Query("INSERT INTO clients (ClientName, ClientCode, ClientURL, ClientAddedOn, ClientStartingYear, AppVersion, Limits_DiskSpace)VALUES ('$sClientName', '$sClientCode', '$sClientURL', '$dNow', '$iClientStartingYear', '$dAppVersion', '$sDiskSpace')");
            if ( $varResult2 )
            {
                $sDBName = "storeviz_".$sClientCode;
                $varResult3 = $objDatabaseMaster->Query("INSERT INTO dbs (DatabaseCode, DatabaseType, HostName, UserName, PASSWORD, `DATABASE`)  VALUES ('F1', 'mysqli', '$sHostName', '$sUserName', '$sPassword', '$sDBName')");
                if ( $varResult3 )
                {
                    $iDatabaseId = $objDatabaseMaster->LastInsertedId();
                    $varResult4 = $objDatabaseMaster->Query("UPDATE clients SET DatabaseId = '$iDatabaseId' WHERE ClientCode = '$sClientCode'");
                    if ( $varResult4 )
                    {
                        //$cConnection = new mysqli("storeviz.com", "stviz9x9x", "kkmm2D@z5A2", );
                        $cMasterConnection = new mysqli( $sHostName, $sUserName, $sPassword);
                        // Check connection
                        if ( !$cMasterConnection->connect_error )
                        {
                            $varResult5 = $cMasterConnection->query("CREATE DATABASE $sDBName");
                            if ( $varResult5 )
                            {
                                $cFreshDBConnection = new mysqli( $sHostName, $sUserName, $sPassword, "storeviz_fresh");
                                // connect to first DB
                                $tables = $cFreshDBConnection->query("SHOW TABLES") or die($cClientConnection->error);
                                while ($row = mysqli_fetch_assoc($tables))
                                {
                                    foreach($row as $value)
                                    {
                                        $aTables[] = $value;
                                    }
                                }
                                
                                $originalDB = "storeviz_fresh";
                                // connect to second DB
                                //$cClientConnection = new mysqli("localhost", "root", "", $sDBName);
                                $cClientConnection = new mysqli( $sHostName, $sUserName, $sPassword, $sDBName);
                                
                                foreach ($aTables as $table)
                                {
                                    $cClientConnection->query("CREATE TABLE $table LIKE ".$originalDB.".".$table) or die($cClientConnection->error);
                                    $cClientConnection->query("INSERT INTO $table SELECT * FROM ".$originalDB.".".$table) or die($cClientConnection->error);
                                }

                                $sDataFolder = cDataFolder."/";
                                $sClientFolder = $sDataFolder.$sClientCode."/";
                                //$fileTempName = "http://storeviz.com/system/SVData/NoPhoto.jpg";
                                //$sClientFolder."systemlog/".date("F Y").".log";
                                $fileTempName = cDataFolder."/hi.txt";
                                mkdir($sClientFolder);
                                chmod($sClientFolder, 0777);

                                
                                mkdir($sClientFolder."json_backups");
                                chmod($sClientFolder."json_backups", 0777);
                                
                                mkdir($sClientFolder."systemlog");
                                chmod($sClientFolder."systemlog", 0777);
                                $handle = fopen($sClientFolder."systemlog/".date("F Y").".log", "a+");
                                fclose($handle);



                                mkdir($sClientFolder."stores");
                                chmod($sClientFolder."stores", 0777);
                                
                                mkdir($sClientFolder."merchandising_drives");
                                chmod($sClientFolder."merchandising_drives", 0777);
                                
                                mkdir($sClientFolder."visibility_tools");
                                chmod($sClientFolder."visibility_tools", 0777);
                                
                                mkdir($sClientFolder."shop_close_images");
                                chmod($sClientFolder."shop_close_images", 0777);
                                
                                mkdir($sClientFolder."competitor_updates");
                                chmod($sClientFolder."competitor_updates", 0777);
                               
                                
                                mkdir($sClientFolder."module_type");
                                chmod($sClientFolder."module_type", 0777);
                                mkdir($sClientFolder."module_type/audios");
                                chmod($sClientFolder."module_type/audios", 0777);
                                mkdir($sClientFolder."module_type/images");
                                chmod($sClientFolder."module_type/images", 0777);
                                mkdir($sClientFolder."module_type/videos");
                                chmod($sClientFolder."module_type/videos", 0777);
                                
                                mkdir($sClientFolder."set_of_instructions");
                                chmod($sClientFolder."set_of_instructions", 0777);
                                mkdir($sClientFolder."set_of_instructions/audios");
                                chmod($sClientFolder."set_of_instructions/audios", 0777);
                                mkdir($sClientFolder."set_of_instructions/images");
                                chmod($sClientFolder."set_of_instructions/images", 0777);
                                mkdir($sClientFolder."set_of_instructions/videos");
                                chmod($sClientFolder."set_of_instructions/videos", 0777);
                                /*$fileTempName = "E:/xampp/htdocs/Retailistan/StoreViz/system/library/SVA/Clients/hi.txt";
                               $folderName = 'hyderabad/testi/';  // path on s3 bucket.
                               $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName ,S3::ACL_PUBLIC_READ );
                                */
                                $folderName = $sClientCode."/";
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."temp/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."json_backups/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."stores/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."merchandising_drives/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."visibility_tools/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."shop_close_images/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."competitor_updates/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."module_type/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."module_type/audios/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."module_type/videos/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."module_type/images/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."set_of_instructions/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."set_of_instructions/audios/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."set_of_instructions/images/" ,S3::ACL_PUBLIC_READ_WRITE );
                                $objS3->putObjectFile($fileTempName,"storeviz.com" ,$folderName."set_of_instructions/videos/" ,S3::ACL_PUBLIC_READ_WRITE );

                                $dNowNew = $objGeneral->fnNow();
                                $cClientConnection->query("UPDATE clients SET ClientCode = '$sClientCode', ClientName = '$sClientName', ClientStartingYear = '$iClientStartingYear', ClientAddedOn = '$dNowNew'");
                                if ($cClientConnection)
                                {

                                $sFileName = $_FILES['simage'];
                                $sFileTmp = $_FILES['simage']['tmp_name'];
                                $sImageName = "logo.png";

                                    if(move_uploaded_file($sFileTmp, $sClientFolder.$sImageName))
                                    {
                                        
                                    }else
                                    {
                                        copy($sDataFolder.'NoPhoto.jpg', $sClientFolder.'logo.png');
                                    }
                                    return 1;

                                }
                                else
                                    return 2;
                            }
                        }
                        else
                            return 2;
                    }
                    else
                        return 2;
                    
                }
                else
                    return 2;
            }
            else
                return 2;
            
            return 1;
        }
        else
            return 2;
        
    }


    function EditClient()
    {


        global $objDatabaseMaster;
        global $objGeneral;
        

      
       $sClientName =  $objGeneral->fnGet("cn");
       $sClientCode =  $objGeneral->fnGet("ccode");
       $sDiskSpace =  $objGeneral->fnGet("dsp");

       // $varResult4 = $objDatabaseMaster->Query("UPDATE clients SET ClientName = '$sClientName' , Limits_DiskSpace = '$sDiskSpace' WHERE ClientCode = '$sClientCode'");
        if ($varResult4)
        {
                
            $sFileName = $_FILES['simage'];
            $sFileTmp = $_FILES['simage']['tmp_name'];
            $sImageName = "logo.png";
            
            
            $sTargetDir = cDataFolder.'/'.$sClientCode.'/';


            if(move_uploaded_file($sFileTmp, $sTargetDir.$sImageName))
            {
                
            }
            return 1;
        }
        else
            return 2;
        
    }

      function ClientDataList($iDatabaseId, $sTableName, $sCSV="")
    {
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;



        
        
       
        $sReturn ='<style><style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            text-align: left;
            padding: 8px;
        }
        
        tr:nth-child(even){background-color: #f2f2f2}
        
        th {
            background-color: #4CAF50;
            color: white;
        }
        </style></style>';
        
        //  $dNow = $objGeneral->fnNow();
        // $sReturn .= '<div style="margin-left:876px;position: relative;top: -50px;"><button style="border-radius: 50%;" class="btn btn-primary btn-sm" onclick="DwonloadCSV();"  ><i class="fa fa-download" aria-hidden="true" ></i> &nbsp;Dwonload CSV</button></div>'
        // . '<table style="width:100%;">' ;
         $sReturn .= '<table style="width:100%;">' ;
        //
        if($sTableName=="stores")
        {
            
            
            $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT C.ChannelName, T.TownName,S.StoreName,S.StoreCode ,S.Address, S.ContactPersonName, S.ContactPersonMobileNumber
            FROM stores AS S  INNER JOIN channels AS C
            ON C.ChannelId = S.ChannelId  INNER JOIN towns AS T
            ON T.TownId = S.TownId ");
            
            $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
            $sReturn.= '<th>S#</th>
                        <th>Store Name</th>
                        <th>Store Code</th>
                        <th>Channel Name</th>
                        <th>Town Name</th>                        
                        <th>Address</th>
                        <th>Contact Person</th>
                        <th>CP Mobile Number</th>';
            for ($k=0; $k < $iTotalRows; $k++)
            {
                $sChannelName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ChannelName");
                $sTownName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "TownName");
                $sStoreName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreName");
                $sStoreCode = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreCode");
                $sAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "Address");
                $sContactPersonName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonName");
                $sContactPersonMobileNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonMobileNumber");
                $sReturn.= '<tr><td>'.++$k.'</td>
                <td>'.$sStoreName.'</td>
                <td>'.$sStoreCode.'</td>
                <td>'.$sChannelName.'</td>
                <td>'.$sTownName.'</td>
                <td>'.$sAddress.'</td>
                <td>'.$sContactPersonName.'</td>
                <td>'.$sContactPersonMobileNumber.'</td>
                </tr>';
                
                
            }//loop end
            
            
            
            if($sCSV == $sTableName."CSV")
            {
                $sReturn = $this->DwonloadStoreCSV($iDatabaseId);
                
                return $sReturn ;
            }
        }
        else if($sTableName=="stores_raw")
        {
            $sReturn.= '<th>S#</th>
                       
                        
                       
                        <th>Store Name</th>
                        <th>Store Code</th>
                        <th>Channel Name</th>
                        <th>Auditor Name</th>
                        <th>Town Name</th>
                        <th>Address</th>
                        <th>Contact Person Name</th>
                        <th>CP Mobile Number</th>';
            
            $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT A.FirstName, A.LastName, C.ChannelName, T.TownName,S.StoreName,S.StoreCode ,S.Address, S.ContactPersonName, S.ContactPersonMobileNumber
            FROM stores_raw AS S  INNER JOIN channels AS C
            ON C.ChannelId = S.ChannelId
            INNER JOIN auditors AS A
            ON A.AuditorId = S.AuditorId
            INNER JOIN stores_tagging_sub_locality AS SL
            ON SL.SubLocalityId = S.SubLocalityId
            INNER JOIN stores_tagging_locality AS L
            ON L.LocalityId = SL.LocalityId
            INNER JOIN stores_tagging_towns AS T
            ON T.TownId = L.TownId " );
            
            $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
            //$sReturn.= '<th>S#</th><th>Channel Name</th><th>Twon Name</th><th>Store Name</th><th>Store Code</th><th>Address</th><th>Contact Person Name</th><th>Contact PersonMobile Number</th>';
            for ($k=0; $k < $iTotalRows; $k++)
            {
                $sFirstName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "FirstName");
                $sLastName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "LastName");
                $sChannelName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ChannelName");
                $sTownName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "TownName");
                $sStoreName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreName");
                $sStoreCode = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreCode");
                $sAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "Address");
                $sContactPersonName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonName");
                $sContactPersonMobileNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonMobileNumber");
                $sReturn.= '<tr><td>'.++$k.'</td>
                <td>'.$sStoreName.'</td>               
                <td>'.$sStoreCode.'</td>
                <td>'.$sChannelName.'</td>                
                <td>'.$sFirstName.' '.$sLastName.'</td>
                <td>'.$sTownName.'</td>
                <td>'.$sAddress.'</td>
                <td>'.$sContactPersonName.'</td>
                <td>'.$sContactPersonMobileNumber.'</td>
                </tr>';
                
                
            }
            if($sCSV == $sTableName."CSV")
            {
                $sReturn = $this->DownloadStoreRawCSV($iDatabaseId);
                return $sReturn ;
            }
            
            
        }
        
        else if($sTableName=="skus")
        {
            $sReturn.= '<th>S#</th>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>Brand Name</th>
                        <th>Category Name</th>';
            
            $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT  SB.BrandName, SC.CategoryName,  S.SKUName, S.SKUCode
            FROM skus AS S
            INNER JOIN skus_brands AS SB
            ON SB.BrandId = S.BrandId
            INNER JOIN skus_categories AS SC
            ON SC.CategoryId=S.CategoryId " );
            
            $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
            //$sReturn.= '<th>S#</th><th>Channel Name</th><th>Twon Name</th><th>Store Name</th><th>Store Code</th><th>Address</th><th>Contact Person Name</th><th>Contact PersonMobile Number</th>';
            for ($k=0; $k < $iTotalRows; $k++)
            {
                $sBrandName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "BrandName");
                $sCategoryName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "CategoryName");
                $sSKUName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SKUName");
                $sSKUCode = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SKUCode");
                $sReturn.= '<tr><td>'.++$k.'</td>
                <td>'.$sSKUName.'</td>
                <td>'.$sSKUCode.'</td>
                <td>'.$sBrandName.'</td>
                <td>'.$sCategoryName.'</td>
                </tr>';
            }
            
            if($sCSV == $sTableName."CSV")
            {
                $sReturn = $this->DownloadSKUCSV($iDatabaseId);
                return $sReturn ;
            }
        }
        
        else if($sTableName=="supervisors")
        {
            $sReturn.= '
            <th>S#</th>
            <th>Supervisor Name</th>
            <th>CNIC Number</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>Address</th>';
            
            $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT S.SupervisorName,S.Address,
            S.ContactNumber,S.EmailAddress, S.CNICNumber
            FROM supervisors AS S  " );
            
            $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
            //$sReturn.= '<th>S#</th><th>Channel Name</th><th>Twon Name</th><th>Store Name</th><th>Store Code</th><th>Address</th><th>Contact Person Name</th><th>Contact PersonMobile Number</th>';
            for ($k=0; $k < $iTotalRows; $k++)
            {
                $sSupervisorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SupervisorName");
                $iCNICNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "CNICNumber");
                $iContactNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactNumber");
                $sEmailAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "EmailAddress");
                $sAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "Address");
                
                $sReturn.= '<tr><td>'.++$k.'</td>
                <td>'.$sSupervisorName.'</td>
                <td>'.$iCNICNumber.'</td>
                <td>'.$iContactNumber.'</td>
                <td>'.$sEmailAddress.'</td>
                <td>'.$sAddress.'</td>
                </tr>';
            }
            if($sCSV == $sTableName."CSV")
            { $sReturn = $this->DownloadSupervisorCSV($iDatabaseId);
                return $sReturn ;
            }
        }
        else if($sTableName=="auditors")
        {
            $sReturn.= '
            <th>S#</th>
            <th>Auditor Name</th>
            <th>Supervisor Name</th>
            <th>Contact Number</th>
            <th>CNICNumber</th>';
            
            $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT  S.SupervisorName, CONCAT(A.FirstName, ' ', A.LastName) AS 'AuditorName',  A.CNICNumber, A.MobileNumber
            FROM  auditors AS A
            INNER JOIN supervisors AS S
            ON S.SupervisorId = A.SupervisorId  " );
            
            $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
            //$sReturn.= '<th>S#</th><th>Channel Name</th><th>Twon Name</th><th>Store Name</th><th>Store Code</th><th>Address</th><th>Contact Person Name</th><th>Contact PersonMobile Number</th>';
            for ($k=0; $k < $iTotalRows; $k++)
            {
                $sSupervisorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SupervisorName");
                $sAuditorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "AuditorName");
                $iCNICNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "CNICNumber");
                $sMobileNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "MobileNumber");
                
                
                $sReturn.= '<tr><td>'.++$k.'</td>
                 <td>'.$sAuditorName.'</td>
                <td>'.$sSupervisorName.'</td>               
                <td>'.$iCNICNumber.'</td>
                <td>'.$sMobileNumber.'</td>
                </tr>';
            }
            if($sCSV == $sTableName."CSV")
            { $sReturn = $this->DownloadAuditorCSV($iDatabaseId);
                return $sReturn ;
            }
        }
        
        
        
        $sReturn .= '</table>';
        return ($sReturn);
    }// fucntion end
    



   function PieChart($sClientName,$iStores,$sRawStore,$sSupervisor,$sMerchandiser,$sSkus,$c)
    {
        try {
        
        $sRetrun='<script type="text/javascript">
try {
        $(function () {
            $("#" + "'.$sClientName.$c.' ").highcharts({
                chart: {
                    type: "pie",
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: "'.$sClientName.' "
                },
                subtitle: {
                    text: ""
                },
                plotOptions: {
                    pie: {
                        innerSize: 100,
                        depth: 45
                    }
                },
                series: [{
                    name: "",
                    data: [
                    ["Supervisor", '.$sSupervisor.'],
                    ["Auditors", '.$sMerchandiser.'],
                    ["Stores", '.$iStores.'],
                    ["Products/SKU", '.$sSkus.'],
                    ["Raw Stores", '.$sRawStore.']
                   
                    
                    ]
                }]
            });
        });
        }
catch(err) {
    document.getElementById("demo").innerHTML = err.message;
    console.log( err.message);
}
    
       $(document).ready(function(){
        $(".highcharts-button").css("display","none");
       });
        </script>
        
       
        ';
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return($sRetrun);
    }

   /* function PieChart($sClientName,$iStores,$sRawStore,$sSupervisor,$sMerchandiser,$sSkus,$c)
    {


            $sRetrun='<script type="text/javascript">
                var ctx = $("#" + "'.$sClientName.$c.' ");
              var myChart = new Chart( ctx, {
              type: "doughnut",
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: "# of Votes",
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                "rgba(255, 99, 132, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)"
            ],
            borderColor: [
                "rgba(255,99,132,1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)",
                "rgba(153, 102, 255, 1)",
                "rgba(255, 159, 64, 1)"
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
              });
        </script>';

        return($sRetrun);
    }
*/





    function DownloadAuditorCSV($iDatabaseId)
    {
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;
        
        
        //$varResult = $aDatabases[$iDatabaseId]->Query
        
        $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT  S.SupervisorName, CONCAT(A.FirstName, ' ', A.LastName) AS 'AuditorName',  A.CNICNumber, A.MobileNumber
        FROM  auditors AS A
        INNER JOIN supervisors AS S
        ON S.SupervisorId = A.SupervisorId  " );
        
        
        $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
        if($iTotalRows > 0)
        {
            $filename = "DataExport_Supervisor.csv";
            
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            
            // setting header of .csv file
            $header = array("Supervisor Name", "Auditor Name ", "CNIC Number","Mobile Number");
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, $header);
            
            $row=array();
            
            for($k=0; $k<$iTotalRows; $k++)
            {
                
                $sSupervisorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SupervisorName");
                $sAuditorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "AuditorName");
                $iCNICNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "CNICNumber");
                $sMobileNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "MobileNumber");
                
                
                $row[0] = $sSupervisorName;
                $row[1] = $sAuditorName;
                $row[2] = $iCNICNumber;
                $row[3] = $sMobileNumber;
                
                
                fputcsv($fp, $row);
            }
            
            fclose($fp);
            
        }
        else
        {
            return "Sorry No Record Found !";
        }
        
        
    }
    
    
    
    function DownloadStoreRawCSV($iDatabaseId)
    {
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;
        
        
        //$varResult = $aDatabases[$iDatabaseId]->Query
        $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT A.FirstName, A.LastName, C.ChannelName, T.TownName,S.StoreName,S.StoreCode ,S.Address, S.ContactPersonName, S.ContactPersonMobileNumber
        FROM stores_raw AS S  INNER JOIN channels AS C
        ON C.ChannelId = S.ChannelId
        INNER JOIN auditors AS A
        ON A.AuditorId = S.AuditorId
        INNER JOIN stores_tagging_sub_locality AS SL
        ON SL.SubLocalityId = S.SubLocalityId
        INNER JOIN stores_tagging_locality AS L
        ON L.LocalityId = SL.LocalityId
        INNER JOIN stores_tagging_towns AS T
        ON T.TownId = L.TownId " );
        
        
        
        $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
        if($iTotalRows > 0)
        {
            $filename = "DataExport_Supervisor.csv";
            
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            
            // setting header of .csv file
            $header = array("Auditor Name", "Channel Name ", "Town Name","Store Name","Store Code","Address","ContactPersonName","ContactPersonMobileNumber");
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, $header);
            
            $row=array();
            
            for($k=0; $k<$iTotalRows; $k++)
            {
                
                $sFirstName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "FirstName");
                $sLastName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "LastName");
                $sChannelName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ChannelName");
                $sTownName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "TownName");
                $sStoreName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreName");
                $sStoreCode = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "StoreCode");
                $sAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "Address");
                $sContactPersonName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonName");
                $sContactPersonMobileNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactPersonMobileNumber");
                
                
                $row[0] = $sFirstName.' '.$sLastName;
                $row[1] = $sChannelName;
                $row[2] = $sTownName;
                $row[3] = $sStoreName;
                $row[4] = $sStoreCode;
                $row[5] = $sAddress;
                $row[6] = $sContactPersonName;
                $row[7] = $sContactPersonMobileNumber;
                fputcsv($fp, $row);
            }
            
            fclose($fp);
            
        }
        else
        {
            return "Sorry No Record Found !";
        }
        
        
    }
    
    
    
    function DownloadSupervisorCSV($iDatabaseId)
    {
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;
        
        
        //$varResult = $aDatabases[$iDatabaseId]->Query
        $varResult1 = $aDatabases[$iDatabaseId]->Query("SELECT S.SupervisorName,S.Address,
        S.ContactNumber,S.EmailAddress, S.CNICNumber
        FROM supervisors AS S  " );
        
        
        
        $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult1);
        if($iTotalRows > 0)
        {
            $filename = "DataExport_Supervisor.csv";
            
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            
            // setting header of .csv file
            $header = array("Supervisor Name", "CNIC ", "Contact Number","Email","Address");
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, $header);
            
            $row=array();
            
            for($k=0; $k<$iTotalRows; $k++)
            {
                
                $sSupervisorName = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "SupervisorName");
                $iCNICNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "CNICNumber");
                $iContactNumber = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "ContactNumber");
                $sEmailAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "EmailAddress");
                $sAddress = $aDatabases[$iDatabaseId]->Result($varResult1, $k, "Address");
                
                
                $row[0] = $sSupervisorName;
                $row[1] = $iCNICNumber;
                $row[2] = $iContactNumber;
                $row[3] = $sEmailAddress;
                $row[4] = $sAddress;
                
                fputcsv($fp, $row);
            }
            
            fclose($fp);
            
        }
        else
        {
            return "Sorry No Record Found !";
        }
        
        
    }
    
    
    function DownloadSKUCSV($iDatabaseId)
    {
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;
        
        
        //$varResult = $aDatabases[$iDatabaseId]->Query
        $varResult = $aDatabases[$iDatabaseId]->Query("SELECT SK.SKUName, SK.SKUCode, SB.BrandName, SC.CategoryName FROM skus AS SK
        INNER JOIN skus_brands AS SB ON SB.BrandId=SK.BrandId
        INNER JOIN skus_categories AS SC ON SC.CategoryId=SK.CategoryId");
        
        $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult);
        if($iTotalRows > 0)
        {
            $filename = "DataExport_SKUs.csv";
            
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            
            // setting header of .csv file
            $header = array("SKU Name", "SKU Code", "Brand Name","Category Name");
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, $header);
            
            $row=array();
            
            for($i=0; $i<$iTotalRows; $i++)
            {
                $sSKUName = $aDatabases[$iDatabaseId]->Result($varResult, $i, "SKUName");
                $sSKUCode = $aDatabases[$iDatabaseId]->Result($varResult, $i, "SKUCode");
                $sBrandName = $aDatabases[$iDatabaseId]->Result($varResult, $i, "BrandName");
                $sCategoryName = $aDatabases[$iDatabaseId]->Result($varResult, $i, "CategoryName");
                
                $row[0] = $sSKUName;
                $row[1] = $sSKUCode;
                $row[2] = $sBrandName;
                $row[3] = $sCategoryName;
                fputcsv($fp, $row);
            }
            
            fclose($fp);
            
        }
        else
        {
            return "Sorry No Record Found !";
        }
        
        
    }
    
    function DwonloadStoreCSV($iDatabaseId)
    {
        
        global $objDatabaseMaster;
        global $aDatabases;
        global $objGeneral;
        
        
        $varResult = $aDatabases[$iDatabaseId]->Query("SELECT
        CH.ChannelName,
        T.TownName,
        S.StoreName,
        S.StoreCode,
        ST.StoreType,
        S.SubElementName,
        S.Status,
        S.Location,
        S.SubLocation,
        S.Address,
        S.ContactPersonName,
        S.ContactPersonMobileNumber,
        S.Latitude,
        S.Longitude,
        S.DSRName,
        S.TerritorySalesOfficer,
        S.AreaSalesManager,
        S.RegionalSalesManager,
        S.SalesManager,
        S.DistributorName
        FROM
        stores AS S
        INNER JOIN stores_type AS ST
        ON ST.StoreTypeId = S.StoreTypeId
        INNER JOIN towns AS T
        ON T.TownId = S.TownId
        INNER JOIN channels AS CH
        ON CH.ChannelId = S.ChannelId");
        
        $iTotalRows = $aDatabases[$iDatabaseId]->RowsNumber($varResult);
        
        if($iTotalRows > 0)
        {
            $filename = "DataExport_StoresData.csv";
            
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            
            // setting header of .csv file
            $header = array("Channel Name", "Town Name", "Store Name", "Store Code", "Store Type", "Sub Element Name", "Status", "Location", "Sub Location", "Address", "Contact Person Name", "Contact Person Mobile Number", "Latitude", "Longitude", "DSR Name", "Territory Sales Officer", "Area Sales Manager", "Regional Sales Manager", "Sales Manager", "Distributor Name");
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, $header);
            
            
            $row = array();
            for($i=0; $i<$iTotalRows; $i++)
            {
                $row[1] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "ChannelName");
                $row[2] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "TownName");
                $row[3] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "StoreName");
                $row[4] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "StoreCode");
                $row[5] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "StoreType");
                $row[6] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "SubElementName");
                $row[7] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "Status");
                $row[8] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "Location");
                $row[9] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "SubLocation");
                $row[10] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "Address");
                $row[11] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "ContactPersonName");
                $row[12] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "ContactPersonMobileNumber");
                $row[13] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "Latitude");
                $row[14] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "Longitude");
                $row[15] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "DSRName");
                $row[16] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "TerritorySalesOfficer");
                $row[17] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "AreaSalesManager");
                $row[18] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "RegionalSalesManager");
                $row[19] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "SalesManager");
                $row[20] = $aDatabases[$iDatabaseId]->Result($varResult, $i, "DistributorName");
                
                fputcsv($fp, $row);
            }
            
            fclose($fp);
            
        }
        
        
        
    }

}
?>