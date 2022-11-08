<?php
$usuario = $_SESSION['user_logado'];
require_once 'Upload.php';
require_once 'Conexao.php';

if(isset($_POST['btnImg'])){
    $up = new Upload($_FILES['foto'],'img/');
    $url_img = $up->salvarImagem();

    $cmdSql = "INSERT INTO imagem(link, fk_usuario_email) VALUES (:url_img, :email)";
    $dados = [
        ':email' => $usuario->email,
        ':url_img' => $url_img
    ];
    $cxPronta = $cx->prepare($cmdSql); 
    if($cxPronta->execute($dados)){
        echo'<div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Cadastro</h4>
            <p>Imagem cadastrada com sucesso</p>
        </div>';
    }
    else{
        echo'<div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Cadastro</h4>
            <p>Erro ao cadastrar imagem</p>
        </div>';
    }     
        
}

if(isset($_POST['btnDelete'])){
    $cmdSql = 'CALL imagem_excluir(:link)';
    $link = $_POST['btnDelete'];
    
    $cxPreparado = $cx->prepare($cmdSql);
    if(!$cxPreparado->execute([':link'=>$link])){
        echo'<div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Exclusão de imagem</h4>
            <p>Erro ao deletar a imagem</p>
        </div>';
    }
}
if(isset($_POST['btnUserDelete'])){
    //$_SESSION['user_logado']->email;
    $_SESSION['user_logado'] = 'CALL usuario_excluir(:email)';
    $email = $_POST['btnUserDelete'];
    var_dump($email);
    $cxPreparado = $cx->prepare($cmdSql);
    if(!$cxPreparado->execute([':email'=>$email])){
        echo'<div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Exclusão de usuário</h4>
            <p>Erro ao deletar o usuário</p>
        </div>';
    }
}

?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Cadastrar Imagens</a>
        
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Ferramentas</a>
    
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Alterações de Imagem</a>
        
  </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="container tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <h4 class="text-secondary">E aí <?php echo $usuario->nome; ?>, que tal postar suas fotos favoritas?</h4 class="text-secondary">
        
        <form method="POST" class="form-inline" enctype="multipart/form-data"> 
            <input type="file" class="form-control" name="foto" >            
            <button type="submit" name="btnImg" class="btn btn-primary form-control">Enviar IMG</button>
        </form>

        <fieldset>
            <legend>Minha fotos</legend>
            <div class="card-columns">
                <?php
                    $cmdSql = "SELECT * FROM imagem WHERE imagem.fk_usuario_email = :email";
                    $cxPronta = $cx->prepare($cmdSql); 
                    if($cxPronta->execute([':email'=>$usuario->email])){
                        if($cxPronta->rowCount() > 0){
                            $fotos = $cxPronta->fetchAll(PDO::FETCH_OBJ);
                            foreach ($fotos as $foto) {
                                echo'
                                    <div class="card">
                                        <img class="card-img-top" src="'.$foto->link.'">
                                        <form method="post">
                                            <button type="submit" value="'.$foto->link.'" name="btnDelete">DELETE</button>
                                        </form>
                                    </div>';
                            }
                        }
                    }
                ?>
            </div>

        </fieldset>
    </div>

    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <h1>Configurações de Usuário</h1>
         <div class="card-columns">
         </div>
    </div>
    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Altere Imagens
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Foto de Capa</a>
                <a class="dropdown-item" href="#">Foto de Perfil</a>
                <a class="dropdown-item" href="#">Something else here</a>
            </div>
        </div>
    </div>
</div>