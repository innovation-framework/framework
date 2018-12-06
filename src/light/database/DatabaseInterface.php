<?php
namespace Light\Database;

/**
 * This class forces subclass DB implement follow to this pattern
 */
interface DatabaseInterface
{
    public function select();

    public function insert($obj);

    public function update();

    public function delete();
}