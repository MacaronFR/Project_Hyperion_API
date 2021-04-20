<?php


namespace Hyperion\API;

require_once "autoload.php";

class ReferenceModel extends Model{
	protected string $id_name = "id_reference";
	protected string $table_name = "REFERENCE_PRODUCTS";
	protected array $column = [
		"selling" => "selling_price",
		"buying" => "buying_price",
		"type" => "type"
	];

	public function selectAllByType(int $id_type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT RP.id_product as id FROM REFERENCE_PRODUCTS RP WHERE RP.type=:id LIMIT $start, 500;";
		return $this->prepared_query($sql, ["id" => $id_type]);
	}

	public function selectAllByMark(string $mark, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT REFERENCE_PRODUCTS.id_product as id FROM REFERENCE_PRODUCTS
    				INNER JOIN REF_HAVE_SPEC RHS on REFERENCE_PRODUCTS.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"mark\" AND value=:mark LIMIT $start, 500;";
		return $this->prepared_query($sql, ["mark" => $mark]);
	}

	public function selectAllByTypeMark(int $type, string $mark, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT REFERENCE_PRODUCTS.id_product as id FROM REFERENCE_PRODUCTS
    				INNER JOIN REF_HAVE_SPEC RHS on REFERENCE_PRODUCTS.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"mark\" AND value=:mark AND type=:type LIMIT $start, 500;";
		return $this->prepared_query($sql, ["type" => $type, "mark" => $mark]);
	}

	public function selectWithDetail(int $id): array|false{
		$sql1 = "SELECT S.* FROM REFERENCE_PRODUCTS RP INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE RP.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$ref = [];
		if($ref_spec !== false){
			foreach($ref_spec as $spec){
				if(isset($ref['spec'][$spec['name']])){
					if(is_array($ref['spec'][$spec['name']])){
						$ref['spec'][$spec['name']][] = $spec["value"];
					}else{
						$ref['spec'][$spec['name']] = [$ref['spec'][$spec['name']], $spec["value"]];
					}
				}else{
					$ref["spec"][$spec["name"]] = $spec["value"];
				}
			}
			return $ref;
		}
		return false;
	}
}