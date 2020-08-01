<?php
class Product {
	private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "";
    private $database  = "tms";   
	private $productTable = 'hotels';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}		

	public function searchProducts(){
		$sqlQuery = "SELECT * FROM ".$this->productTable."";
		if(isset($_POST["minPrice"], $_POST["maxPrice"]) && !empty($_POST["minPrice"]) && !empty($_POST["maxPrice"])){
			$sqlQuery .= "
			AND h_price/person BETWEEN '".$_POST["minPrice"]."' AND '".$_POST["maxPrice"]."'";
		}
		if(isset($_POST["feature"])) {
			$FeatureFilterData = implode("','", $_POST["feature"]);
			$sqlQuery .= "
			AND feature IN('".$FeatureFilterData."')";
		}
		$sqlQuery .= " ORDER By h_price/person";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$totalResult = mysqli_num_rows($result);
		$searchResultHTML = '';
		if($totalResult > 0) {
			while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				$searchResultHTML .= '
				<div class="col-sm-4 col-lg-3 col-md-3">
				<div class="product">
				<!--<img src="images/'. $row['image'] .'" alt="" class="img-responsive" >-->
				<p align="center"><strong><a href="#">'. $row['h_name'] .'</a></strong></p>
				<h4 style="text-align:center;" class="text-danger" >'. $row['price'] .'</h4>
				<p>Camera : '. $row['h_price/person'].' MP<br />
				Brand : '. $row['h_features'] .' <br />
				</div>
				</div>';
			}
		} else {
			$searchResultHTML = '<h3>No product found.</h3>';
		}
		return $searchResultHTML;	
	}	
}
?>