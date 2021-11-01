<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m211101_191040_create_user_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {


    $this->createTable('{{%user}}', [
      'id' => $this->primaryKey(),
      'username' => $this->string()->notNull(),
      'passwordHash' => $this->string()->notNull(),
      'authKey' => $this->string(32)->notNull(),
    ]);

    $this->insert('{{%user}}', [
      'id' => '1',
      'username' => 'sh_barcelona',
      'passwordHash' => '$2y$13$Kggcn1Iq1JWw5gFjbJuv.OoO22yaXiquSjQZy75fr6e/PTwsLSuBG',
      'authKey' => 'QbIzc4KVqQdPZkU4-rFK18Hct32lmVt'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%user}}');
  }
}
