 $ . f n . p n g F i x   =   f u n c t i o n ( )   { 
     i f   ( ! $ . b r o w s e r . m s i e   | |   $ . b r o w s e r . v e r s i o n   > =   9 )   {   r e t u r n   $ ( t h i s ) ;   } 
 
     r e t u r n   $ ( t h i s ) . e a c h ( f u n c t i o n ( )   { 
         v a r   i m g   =   $ ( t h i s ) , 
                 s r c   =   i m g . a t t r ( ' s r c ' ) ; 
 
         i m g . a t t r ( ' s r c ' ,   ' / i m a g e s / g e n e r a l / t r a n s p a r e n t . g i f ' ) 
                 . c s s ( ' f i l t e r ' ,   " p r o g i d : D X I m a g e T r a n s f o r m . M i c r o s o f t . A l p h a I m a g e L o a d e r ( e n a b l e d = ' t r u e ' , s i z i n g M e t h o d = ' c r o p ' , s r c = ' "   +   s r c   +   " ' ) " ) ; 
     } ) ; 
 } ;