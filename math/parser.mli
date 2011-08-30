type token =
  | LITERAL of (Render_info.t)
  | DELIMITER of (Render_info.t)
  | FUN_AR2 of (string)
  | FUN_INFIX of (string)
  | FUN_AR1 of (string)
  | DECL of (string)
  | FUN_AR1opt of (string)
  | BIG of (string)
  | FUN_AR2nb of (string)
  | BOX of (string*string)
  | FUN_AR1hl of (string*(string*string))
  | FUN_AR1hf of (string*Render_info.font_force)
  | DECLh of (string*Render_info.font_force)
  | FUN_AR2h of (string*(Tex.t->Tex.t->string*string*string))
  | FUN_INFIXh of (string*(Tex.t list->Tex.t list->string*string*string))
  | EOF
  | CURLY_OPEN
  | CURLY_CLOSE
  | SUB
  | SUP
  | SQ_CLOSE
  | NEXT_CELL
  | NEXT_ROW
  | BEGIN__MATRIX
  | BEGIN_PMATRIX
  | BEGIN_BMATRIX
  | BEGIN_BBMATRIX
  | BEGIN_VMATRIX
  | BEGIN_VVMATRIX
  | BEGIN_CASES
  | BEGIN_ARRAY
  | BEGIN_ALIGN
  | BEGIN_ALIGNAT
  | BEGIN_SMALLMATRIX
  | END__MATRIX
  | END_PMATRIX
  | END_BMATRIX
  | END_BBMATRIX
  | END_VMATRIX
  | END_VVMATRIX
  | END_CASES
  | END_ARRAY
  | END_ALIGN
  | END_ALIGNAT
  | END_SMALLMATRIX
  | LEFT
  | RIGHT

val tex_expr :
  (Lexing.lexbuf  -> token) -> Lexing.lexbuf -> Tex.t list
