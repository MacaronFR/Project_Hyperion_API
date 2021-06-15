<?php


namespace Hyperion\API;
require_once "autoload.php";

class ProductModel extends Model{
	protected string $table_name = "PRODUCTS";
	protected string $id_name = "id_product";
	protected array $column = [
		"state" => "state",
		"sell_p" => "selling_price",
		"buy_p" => "buying_price",
		"status" => "status",
		"offer" => "id_offers",
		"ref" => "id_ref",
		"buy_d" => "buying_date",
		"sell_d" => "selling_date"
	];
	protected int $max_row = 10;

	public function selectAllByType(int $id_type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT P.id_product as id, P.buying_price as buying_price, P.selling_price as selling_price, T.type as type FROM PRODUCTS P 
    				INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product 
    				INNER JOIN TYPES T on RP.type = T.id_type
				WHERE RP.type=:id LIMIT $start, 500;";
		$products = $this->prepared_query($sql, ["id" => $id_type]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectWithDetail(int $id): array|false{
		$sql1 = "SELECT name, S.value FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$sql2 = "SELECT name, value FROM PRODUCTS INNER JOIN PRODUCT_HAVE_SPEC PHS on PRODUCTS.id_product = PHS.id_product INNER JOIN SPECIFICATION S on PHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$prod_spec = $this->prepared_query($sql2, ["id" => $id]);
		$prod = [];
		if($ref_spec !== false && $prod_spec !== false){
			foreach($ref_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			foreach($prod_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			return $prod;
		}
		return false;
	}

	public function selectAllDetails(int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$query1 = "	SELECT P.id_product as id, P.buying_price as buying_price, P.selling_price as selling_price, T.type as type FROM PRODUCTS P 
    					INNER JOIN REFERENCE_PRODUCTS RP ON P.id_ref = RP.id_product
    					INNER JOIN TYPES T on RP.type = T.id_type
					LIMIT $start, 500;";
		$products = $this->query($query1);
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id']));
		}
		return $products;
	}

	public function selectAllByBrand(string $brand, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT P.id_product as id, P.buying_price as buying_price, P.selling_price as selling_price, T.type as type FROM PRODUCTS P
					INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"brand\" AND S.value=:brand LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["brand" => $brand]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeBrand(int $type, string $brand, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type as type FROM PRODUCTS P
					INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"brand\" AND S.value=:brand AND T.id_type=:type LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["type" => $type, "brand" => $brand]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectAllByModel(string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type AS type FROM PRODUCTS P
    				INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND value=:model LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["model" => $model]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeModel(int $type, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type AS type FROM PRODUCTS P
    				INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND value=:model AND RP.type=:type LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["model" => $model, 'type' => $type]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectAllByBrandModel(string $brand, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (SELECT COUNT(P.id_product) as count, P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type as type
					  FROM PRODUCTS P 
							   INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
							   INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							   INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
							   INNER JOIN TYPES T on RP.type = T.id_type
					  WHERE (name = \"model\" AND value = :model)
						 OR (name = \"brand\" AND value = :brand)
					  GROUP BY P.id_product 
					 ) S
				WHERE count=2 LIMIT $start,500;";
		$products = $this->prepared_query($sql, ['model' => $model, 'brand' => $brand]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeBrandModel(int $type, string $brand, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (
				    SELECT COUNT(P.id_product) as count, P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type as type
					FROM PRODUCTS P 
						INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						INNER JOIN TYPES T on RP.type = T.id_type
					WHERE ((name = \"model\" AND value = :model)
						OR (name = \"brand\" AND value = :brand))
						AND RP.type=:type
					GROUP BY P.id_product 
					) S
				WHERE count=2 LIMIT $start,500;";
		$products = $this->prepared_query($sql, ['type' => $type, 'model' => $model, 'brand' => $brand]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$spec = $this->selectWithDetail($prod['id']);
			if($spec === false){
				return false;
			}
			$prod = array_merge($prod, $spec["spec"]);
		}
		return $products;
	}

	public function selectAllByRef(int $ref_id, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT ";
		foreach($this->column as $n =>$c){
			$sql .= "$c as $n, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE id_ref=:ref";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ['ref' => $ref_id]);
	}

	public function selectAll(int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT RES.id, RES.sell_p, RES.buy_p, RES.type, brand, S2.value as model FROM
					(SELECT PRODUCTS.id_product as id, PRODUCTS.selling_price as sell_p, PRODUCTS.buying_price as buy_p, T.type, S.value as brand, id_ref
					FROM PRODUCTS
						INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product
						INNER JOIN TYPES T on RP.type = T.id_type
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					WHERE name=\"brand\") RES
					INNER JOIN REF_HAVE_SPEC RHS2 on RES.id_ref=RHS2.id_product
					INNER JOIn SPECIFICATION S2 on RHS2.id_spec = S2.id_specification
				WHERE name=\"model\"";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllFilter(string $search, string $order, string $sort, int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT RES.id, RES.sell_p, RES.buy_p, RES.type, brand, S2.value as model, state, RES.buy_d, RES.sell_d FROM
					(SELECT PRODUCTS.id_product as id, PRODUCTS.selling_price as sell_p, PRODUCTS.buying_price as buy_p, T.type, S.value as brand, id_ref, state, buying_date as buy_d, selling_date as sell_d
					FROM PRODUCTS
						INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product
						INNER JOIN TYPES T on RP.type = T.id_type
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					WHERE name=\"brand\") RES
					INNER JOIN REF_HAVE_SPEC RHS2 on RES.id_ref=RHS2.id_product
					INNER JOIn SPECIFICATION S2 on RHS2.id_spec = S2.id_specification
				WHERE name=\"model\" AND
				(RES.id LIKE :search OR
				RES.sell_p LIKE :search OR
				RES.buy_p LIKE :search OR
				RES.type LIKE :search OR
				brand LIKE :search OR
				S2.value LIKE :search)
				ORDER BY $sort $order";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		$search = "%$search%";
		return $this->prepared_query($sql, ['search' => $search]);
	}

	public function selectTotalFilter(string $search, string $order, string $sort): false|int{
		$sql = "SELECT count(RES.id) as count, S2.value as model FROM
					(SELECT PRODUCTS.id_product as id, PRODUCTS.selling_price as sell_p, PRODUCTS.buying_price as buy_p, T.type, S.value as brand, id_ref, state, buying_date as buy_d, selling_date as sell_d
					FROM PRODUCTS
						INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product
						INNER JOIN TYPES T on RP.type = T.id_type
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					WHERE name=\"brand\") RES
					INNER JOIN REF_HAVE_SPEC RHS2 on RES.id_ref=RHS2.id_product
					INNER JOIn SPECIFICATION S2 on RHS2.id_spec = S2.id_specification
				WHERE name=\"model\" AND
				(RES.id LIKE :search OR
				RES.sell_p LIKE :search OR
				RES.buy_p LIKE :search OR
				RES.type LIKE :search OR
				brand LIKE :search OR
				S2.value LIKE :search)
				ORDER BY $sort $order";
		$search = "%$search%";
		$total = $this->prepared_query($sql, ['search' => $search], unique: true);
		return $total['count'] ?? false;
	}

	public function selectShop(int $cat = -1, int $type = -1, string $brand = "", array $filter = [], string $order = "id", string $sort = "DESC", $iteration = 0): array|false{
		$nfilter = count($filter);
		$start = $iteration * $this->max_row;
		$sql = "SELECT id, type, selling_price as sell_p, state FROM (SELECT *, COUNT( SH.id ) AS count";
		$sub_sel = "";
		$sub_where = "";
		$where = "";
		$param = [];
		$delimiter = true;
		if($brand !== ""){
			$filter[] = ['brand', $brand];
		}
		if($cat !== -1){
			$sub_where .= "category = $cat" . ((!empty($filter) && $type === -1)?" AND ":"") . ($type !==-1?" AND ":"");
		}
		if($type !== -1){
			$sub_where .= "type = $type" . (empty($filter)?"":" AND (");
		}
		if(!empty($filter)){
			$sub_sel .= ",";
		}
		for($i = 0; $i < count($filter); ++$i){
			if($i === count($filter) - 1){
				$delimiter = false;
			}
			$sub_sel .= "(SELECT COUNT(id) FROM SHOP_FILTER WHERE SHOP_FILTER.id = SH.id AND `name`=:name$i) as total$i" . ($delimiter?',':'');
			$sub_where .= "(`name` = :name$i AND `value` = :value$i)" . ($delimiter?" OR":"");
			$where .= "((count = 2 OR total$i = 1) and `name`=:name$i)" . ($delimiter?" OR":"");
			$param["name$i"] = $filter[$i][0];
			$param["value$i"] = $filter[$i][1];
		}
		if($type !== -1 && !empty($filter)){
			$sub_where .= ")";
		}
		$sql .= $sub_sel;
		$sql .= " FROM SHOP_FILTER SH WHERE " . $sub_where;
		$sql .= " GROUP BY SH.id, SH.`name`, SH.`value`) RES " . (!empty($filter) ? "WHERE " . $where: "");
		$sql .= " GROUP BY id";
		if($nfilter !== 0){
			$sql .= " HAVING COUNT(id)=:nFilter";
			$param['nFilter'] = $nfilter;
		}
		$sql .= " ORDER BY $order $sort LIMIT $start, $this->max_row";
		return $this->prepared_query($sql, $param);
	}

	public function selectShopTotal(int $cat = -1, int $type = -1, string $brand = "", array $filter = [], string $order = "id", string $sort = "DESC"): array|false{
		$sql = "SELECT COUNT(id) as total FROM (SELECT id FROM (SELECT *, COUNT( SH.id ) AS count";
		$sub_sel = "";
		$sub_where = "";
		$where = "";
		$param = [];
		$delimiter = true;
		if($brand !== ""){
			$filter[] = ['brand', $brand];
		}
		if($cat !== -1){
			$sub_where .= "category = $cat" . ((!empty($filter) && $type === -1)?" AND ":"") . ($type !==-1?" AND ":"");
		}
		if($type !== -1){
			$sub_where .= "type = $type" . (empty($filter)?"":" AND (");
		}
		if(!empty($filter)){
			$sub_sel .= ",";
		}
		for($i = 0; $i < count($filter); ++$i){
			if($i === count($filter) - 1){
				$delimiter = false;
			}
			$sub_sel .= "(SELECT COUNT(id) FROM SHOP_FILTER WHERE SHOP_FILTER.id = SH.id AND `name`=:name$i) as total$i" . ($delimiter?',':'');
			$sub_where .= "(`name` = :name$i AND `value` = :value$i)" . ($delimiter?" OR":"");
			$where .= "((count = 2 OR total$i = 1) and `name`=:name$i)" . ($delimiter?" OR":"");
			$param["name$i"] = $filter[$i][0];
			$param["value$i"] = $filter[$i][1];
		}
		if($type !== -1 && !empty($filter)){
			$sub_where .= ")";
		}
		$sql .= $sub_sel;
		$sql .= " FROM SHOP_FILTER SH WHERE " . $sub_where;
		$sql .= " GROUP BY SH.id, SH.`name`, SH.`value`) RES " . (!empty($filter) ? "WHERE " . $where: "");
		$sql .= " GROUP BY id) TOTAL_RES";
		return $this->prepared_query($sql, $param, unique: true);
	}

	public function selectAllFilterShop(string $search, string $order, string $sort, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE status=2 (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%" . $search . "%";
		return $this->prepared_query($sql, ["search" => $search]);
	}

	public function selectTotalShop(): int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE $this->id_name<>0 AND status=2";
		$res = $this->query($sql);
		if($res !== false){
			return (int)$res[0]['count'];
		}
		return false;
	}
}