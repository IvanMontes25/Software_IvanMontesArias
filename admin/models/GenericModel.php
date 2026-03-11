<?php
require_once __DIR__ . '/BaseModel.php';

class GenericModel extends BaseModel
{
    // This model provides direct DB access for views that still
    // contain inline queries (transitional - to be refactored over time)
    public function getDb(): mysqli { return $this->db; }
}
