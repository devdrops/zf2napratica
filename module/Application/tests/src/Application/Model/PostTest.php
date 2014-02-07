<?php

/**
 * Description of PostTest
 * @author Davi Marcondes Moreira <davi.marcondes.moreira@gmail.com>
 */

namespace Application\Model;

use Core\Test\ModelTestCase;
use Application\Model\Post;
use Zend\InputFilter\InputFilterInterface;

class PostTest extends ModelTestCase {
    
    public function testGetInputFilter(){
        $post = new Post();
        $if = $post->getInputFilter();
        $this->assertInstanceOf("Zend\InputFilter\InputFilter", $if);
        return $if;
    }
    
    /**
     * @depends testGetInputFilter
     */
    public function testInputFilterValid($if){
        $this->assertEquals(4, $if->count());
        
        $this->assertTrue($if->has('id'));
        $this->assertTrue($if->has('title'));
        $this->assertTrue($if->has('description'));
        $this->assertTrue($if->has('post_date'));
    }
    
    /**
     * @expectedException Core\Model\EntityException
     */
    public function testInputFilterInvalido(){
        $post = new Post();
        $post->title = 'Zombies reversus ab inferno, nam malum cerebro. '
                     . 'De carne animata corpora quaeritis. Summus sit​​'
                     . ', morbo vel maleficia? De Apocalypsi undead dict'
                     . 'um mauris. Hi mortuis soulless creaturas, imo mo'
                     . 'nstra adventus vultus comedat cerebella viventium.';
    }
    
    public function testInsert(){
        $post = $this->addPost();
        $saved = $this->getTable('Application\Model\Post')->save($post);
        
        $this->assertEquals('A Apple compra a Coderockr', $saved->description);
        $this->assertEquals(1, $saved->id);        
    }
    
    /**
     * @expectedException Core\Model\EntityException
     * @expectedExceptionMessage Input inválido: description = 
     */
    public function testInsertInvalido(){
        $post = new Post();
        $post->title = 'teste';
        $post->description = '';
        
        $saved = $this->getTable('Application\Model\Post')->save($post);
    }
    
    public function testUpdate(){
        $tableGateway = $this->getTable('Application\Model\Post');
        $post = $this->addPost();
        
        $saved = $tableGateway->save($post);
        $id = $saved->id;
        
        $this->assertEquals(1, $id);
        
        $post = $tableGateway->get($id);
        $this->assertEquals('A Apple compra a Coderockr', $post->title);
        
        $post->title = 'Coderockr compra a Apple';
        $updated = $tableGateway->save($post);
        
        $post = $tableGateway->get($id);
        $this->assertEquals('Coderockr compra a Apple', $post->title);
    }
    
    /**
     * @expectedException Core\Model\EntityException
     * @expectedExceptionMessage Could not find row 1
     */
    public function testDelete(){
        $tableGateway = $this->getTable('Application\Model\Post');
        $post = $this->addPost();
        
        $saved = $tableGateway->save($post);
        $id    = $saved->id;
        
        $deleted = $tableGateway->delete($id);
        $this->assertEquals(1, $deleted);
        
        $post = $tableGateway->get($id);
    }
    
    private function addPost(){
        $post = new Post();
        
        $post->title        = 'A Apple compra a Coderockr';
        $post->description  = 'A Apple compra a <b>Coderockr</b><br> ';
        $post->post_date    = date('Y-m-d H:i:s');
        
        return $post;
    }
}
