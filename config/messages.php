<?php

//GERAL
define('MSG_PAGE_NOT_FOUND', 'Página não encontrada!');
define('MSG_INVALID_CEP_OR_NOT_FOUND', 'CEP inválido ou não encontrado.');
define('MSG_ALL_FIELDS_IS_REQUIRED', 'Por favor, preencha todos os campos obrigatórios.');

//WEBHOOK
define('MSG_INVALID_TOKEN', 'Token de segurança inválido.');
define('MSG_INVALID_JSON', 'Dados JSON inválidos.');
define('MSG_FIELDS_ORDER_ID_AND_STATUS_IS_REQUIRED', 'Campos "order_id" e "status" são obrigatórios.');
define('MSG_SEND_CONFIRMATION_EMAIL_CREATED_SUCCESSFULLY', 'criado');
define('MSG_SEND_CONFIRMATION_ARRAY_OPTIONS', ['aprovado','cancelado','enviado','entregue']);


//CARRINHO
define('MSG_INVALID_ITEM_FOR_CART', 'Item inválido para adicionar ao carrinho.');
define('MSG_PRODUCT_ADD_TO_CART', 'Produto adicionado ao carrinho!');
define('MSG_CART_UPDATED', 'Carrinho atualizado.');
define('MSG_ITEM_REMOVED_TO_CART', 'Item removido do carrinho.');
define('MSG_INVALID_DATA_FOR_UPDATE_CART', 'Dados inválidos para atualização do carrinho.');
define('MSG_CART_IS_EMPTY', 'Seu carrinho está vazio.');

//ESTOQUE E VARIAÇÃO
define('MSG_INSUFFICIENT_STOCK_FOR_VARIATION_SELECTED', 'Estoque insuficiente para a variação selecionada.');
define('MSG_PRODUCT_VARIATION_NOT_FOUND', 'Variação do produto não encontrada.');
define('MSG_INVALID_VARIATION_TO_CREATE', 'Variação inválida ao criar produto: ');
define('MSG_INVALID_VARIATION_TO_UPDATE', 'Variação inválida ao atualizar produto: ');
define('MSG_QTTY_EXCEEDED_FOR_VARIATION', 'Quantidade solicitada excede o estoque disponível para esta variação.');


//PRODUTO
define('MSG_PRODUCT_NAME_IS_REQUIRED', 'O nome do produto é obrigatório.');
define('MSG_PRODUCT_NOT_FOUND', 'Produto não encontrado.');
define('MSG_PRODUCT_NOT_FOUND_FOR_UPDATE_THIS_CART', 'Produto não encontrado para atualização do carrinho.');
define('MSG_INVALID_ITEM_FOR_REMOVE', 'Item inválido para remoção.');
define('MSG_PRODUCT_ID_NOT_SPECIFIED_FOR', 'ID do produto não fornecido para ');
define('MSG_PRODUCT_AND_VARIATION_CREATED_SUCESSFULLY', 'Produto e variações criados com sucesso!');
define('MSG_PRODUCT_AND_VARIATION_UPDATED_SUCESSFULLY', 'Produto e variações atualizados com sucesso!');
define('MSG_PRODUCT_DATA_INVALID_TO_UPDATE', 'Dados inválidos para atualização do produto.');
define('MSG_PRODUCT_CREATED_ERROR', 'Erro ao criar o produto.');
define('MSG_PRODUCT_UPDATED_ERROR', 'Erro ao atualizar o produto.');
define('MSG_PRODUCT_DELETED_ERROR', 'Erro ao excluir o produto.');
define('MSG_PRODUCT_DELETED_SUCCESSFULLY', 'Produto excluído com sucesso!');

//PEDIDO
define('MSG_ORDER_PLACEMENT_SUCCESS', 'Pedido realizado com sucesso! o número de pedido é #');
define('MSG_ORDER_PLACEMENT_ERROR', 'Erro ao finalizar o pedido: ');
define('MSG_ORDER_NOT_FOUND', 'Pedido não encontrado.');
define('MSG_ORDER_UPDATED_SUCCESSFULLY', 'Status do pedido atualizado para: ');
define('MSG_ORDER_CANCELD_SUCCESSFULLY', 'Pedido cancelado e estoque restaurado.');
define('MSG_ORDER_CANCELD_ERROR', 'Erro ao cancelar pedido e restaurar estoque: ');
define('MSG_ORDER_UPDATED_ERROR', 'Erro ao atualizar status do pedido.');

//CUPOM
define('MSG_COUPON_REMOVED', 'Cupom removido');
define('MSG_INVALID_COUPON', 'Cupom inválido');
define('MSG_INVALID_COUPON_OR_EXPIRED', 'Este cupom não é válido para o seu carrinho ou expirou.');
define('MSG_ALL_FIELDS_COUPON_REQUIRED', 'Por favor, preencha todos os campos obrigatórios para o cupom.');
define('MSG_COUPON_CREATED_SUCCESS', 'Cupom criado com sucesso!');
define('MSG_COUPON_CREATED_ERROR', 'Erro ao criar o cupom. Verifique se o código já existe.');