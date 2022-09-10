<?php
// ehcp language file
$this->lang['pt_BR']=
array(
'undefined_limittype'=>'Tipo de limite indefinido.',
'norecordfound'=>'Nada/Nenhum registro encontrado.',
'recordcount'=>'Número de Registros:',
'areyousuretodelete'=>'Deseja realmente apagar este registro ? <br>',
'error_occured'=>'Ocorreu um erro ',
'write_yourmessage_here'=>'Escreva sua mensagem aqui',
'name_surname'=>'Apelido',
'enter_yourcontactinfo'=>'Entre com suas informações de contato aqui',
'yourmessage_received'=>'Obrigado. Nós recebemos sua mensagem.',
'search_'=>'Buscar',
'list_all'=>'Listar Todos',
'int_undefined_limittype'=>'erro interno ehcp: tipo de limite indefinido',
'perhaps_db_error'=>'Talvez suas configurações do banco de dados estejam incorretas, você não pode se conectar ao banco de dados; ou sua tabela banco de dados/html perdeu algumas linhas.<br> Você pode editar o arquivo config.php para a senha e usuário no painel do ehcp. <br> Tente<a href=misc/mysqltroubleshooter.php> para problemas com mysql</a> para resolver este problema.',
'welcome to world'=>'Bem vindo ao Mundo...',
'Panel Home'=>'Painel Home/Deselecionar',
'Domain Home'=>'Home do Domínio',
'Domains'=>'Domínios',
'Selected Domain'=>'Domínio Selecionado',
'domain_deactivated_contact_administrator'=>'Este domínio está desativado pelo dono ou administrador. Entre em contato com o administrador do domínio...',
'updatehostsfile'=>'Está máquina é utilizada para acesso Desktop também (Atualize o arquivo hosts)',
'dnsip'=>'dnsip (externo/real/estático para ip do servidor)',
'dnsipv6'=>'dnsip V6(externo/real/estático V6 para ip do servidor)',
'disablecustomhttp'=>'Desabilitar HTTP Customizado',
'disableeditapachetemplate'=>'Desabilitar HTTP Customizados para não administradores',
'disableeditdnstemplate'=>'Desabilitar DNSs Customizados para não administradores',
'turnoffoverquotadomains'=>'Desativar as quotas dos domínios',
'domain_format_wrong'=>'Formato de (Sub)Domínio não aceitável, verifique isso..',

# These are separated to this lang file, to enable editing html of pages easily, for ex, add buttons for functions..
'similar_functions_ftp' => "<a href='?op=addftpuser'>Adicionar Novo FTP</a>, <a href='?op=addftptothispaneluser'>Adicionar FTP Sobre o Meu FTP</a>, <a href='?op=addsubdirectorywithftp'>Adicionar FTP em um subdiretório sobre o nome do domínio</a>, <a href='?op=addsubdomainwithftp'>Adicionar subdomínio com FTP</a>, <a href='?op=add_ftp_special'>Adicionar FTP sobre /home/xxx (administrador)</a>, <a href='net2ftp' target=_blank>WebFtp (Net2Ftp)</a>, <a href='?op=addcustomftp'>Adicionar Conta FTP Customizada (Administradores Somente)</a>, <a href='?op=listallftpusers'>Listar Todos os Usuários do FTP</a> ",
'similar_functions_mysql' => "<a href='?op=domainop&amp;action=listdb'>Listar / Deletar Banco de Dados Mysql</a>, <a href='?op=addmysqldb'>Adicionar Banco de Dados Mysql Db&amp;dbuser</a>, <a href='?op=addmysqldbtouser'>Adicionar banco de dados Mysql para usuário existente</a>, <a href='?op=dbadduser'>Adicionar usuário Mysql para banco de dados existente</a>, <a href='/phpmyadmin' target=_blank>phpMyadmin</a>",
'similar_functions_email' => "<a href='?op=listemailusers'>Listar Usuários de Email / Alterar Senhas</a>, <a href='?op=addemailuser'>Adicionar Usuário de Email</a>, Encaminhamento de Email: <a href='?op=emailforwardings'>Listar</a> - <a href='?op=addemailforwarding'>Adicionar</a>, <a href='?op=bulkaddemail'>Adicionar Grupo de Emails</a>, <a href='?op=editEmailUserAutoreply'>editar resposta automática de Email</a> ,<a href='webmail' target=_blank>Webmail (Squirrelmail)</a>",
'similar_functions_domain' => "<a href='?op=addDomainToThisPaneluser'>Adicionar Domínio para meu usuário FTP (Facilmente)</a> - <a href='?op=adddomaineasy'>Adicionar Email Facilmente (com usuário FTP separado)</a> - <a href='?op=adddomain'>Adicionar Domínio Normalmente (Separar FTP&Painel do Usuário)</a> - <a href='?op=bulkadddomain'>Adicionar Grupo de Domínios</a> - <a href='?op=adddnsonlydomain'>Adicionar hospedagem de DNS soemnte</a> - <a href='?op=adddnsonlydomainwithpaneluser'>Adicionar hospedagem DNS somente com separação do painel do usuário</a>-<br><a href='?op=addslavedns'>Tornar o Domínio um DNS Slave</a> - <a href='?op=removeslavedns'>Remover DNS Slave, se qualquer</a><br><br>Diferença de IP(neste servidor, não em multiservidores): <a href='?op=adddomaineasyip'>Adicionar Domínio Facilmente para um IP diferente</a> - <a href='?op=setactiveserverip'>Setar como Ativo o IP do Servidor Web</a><br>Listar Domínios: <a href='?op=listselectdomain'>listagem curta</a> - <a href='?op=listdomains'>listagem longa</a>",
'similar_functions_redirect' => "<a href='?op=editdomainaliases'>Editar Aliases de Domínio </a>",
'similar_functions_customhttpdns' => "Http Customizado: <a href='?op=customhttp'>Listar</a> - <a href='?op=addcustomhttp'>Adicionar</a>, DNS Customizado: <a href='?op=customdns'>Listar</a> - <a href='?op=addcustomdns'>Adicionar</a> --  Permissões Customizadas: <a href='?op=custompermissions'>Listar</a> - <a href='?op=addcustompermission'>Adicionar</a>",
'similar_functions_subdomainsDirs' => "SubDomínios: <a href='?op=subdomains'>Listar</a> - <a href='?op=addsubdomain'>Adicionar</a> - <a href='?op=addsubdomainwithftp'>Adicionar subdomínio com FTP</a> - <a href='?op=addsubdirectorywithftp'>Adicionar subdiretório com FTP (Sobre o nome do domínio)</a> - <a href='?op=sync_directories'>Sincronizar Diretórios</a>",
'similar_functions_HttpDnsTemplatesAliases' => "<a href='?op=editdnstemplate'>Editar modelo DNS para este domínio</a> - <a href='?op=editapachetemplate'>Editar modelo do apache para este domínio </a> - <a href='?op=editdomainaliases'>Editar Aliases para este domínio </a>",
'similar_functions_panelusers' => "<a href='?op=listpanelusers'>Listar Todos os Usuários do Painel/Clientes</a>, <a href='?op=resellers'>Listar Revendedores</a>, <a href='?op=addpaneluser'>Adicionar Painel do Usuário/Cliente/Revendedor</a>",
'similar_functions_server' => "<a href='?op=listservers'>Listar Servidores/IP's</a> - <a href='?op=addserver'>Adicionar Servidor</a> - <a href='?op=addiptothisserver'>Adicionar IP para este servidor</a> - <a href='?op=setactiveserverip'>setar IP do servidor web como Ativo</a>",
'similar_functions_backup' => "<a href='?op=dobackup'>Backup</a> - <a href='?op=dorestore'>Restaurar</a> - <a href='?op=listbackups'>Listar Backups</a>",
'similar_functions_vps' => "<a href='?op=vps'>Home VPS</a> - <a href='?op=add_vps'>Adicionar novo VPS</a> - <a href='?op=copy_vps_image'>Copiar Imagem do VPS</a> - <a href='?op=settings&group=vps'>Configurações do VPS</a> - <a href='?op=vps&op2=other'>Outras opções do Vps</a>",
'similar_functions_pagerewrite' => "<a href='?op=pagerewrite'>página home reescrita</a> - <a href='?op=pagerewrite&op2=add'>adicionar página reescrita</a>",
'similar_functions_custompermissions' => "<a href='?op=custompermissions'>Listar Permissões Customizadas</a> - <a href='?op=addcustompermission'>Adicionar Permissão Customizada</a>",
'similar_functions_vpn' => "<a href='?op=list_vpn'>Listar Usuários VPN</a> <a href='?op=add_vpn'>Adicionar Usuário VPN</a>",

'similar_functions_options'=> 
"
	<br><a href='?op=options&edit=1'>Editar/Alterar Opções</a><br>
	<br><a href='?op=changemypass'>Alterar Minha Senha</a>
	<br><a href='?op=listpanelusers'>Listar/Adicionar Usuários do Painel/Revendedores</a>
	<br><a href='?op=dosyncdns'>Sincronizar DNS</a>
	<br><a href='?op=dosyncdomains'>Sincronizar Domínios</a><br>
	<br><a href='?op=dosyncftp'>Sincronizar FTP (para diretórios home não padrão)</a><br>
	<hr><a href='?op=advancedsettings'>Configurações Avançadas</a><br><br>
	<br><a href='?op=dofixmailconfiguration'>Corrigir Configurações de Email<br>Corrigir Configurações EHCP</a> (Este é usado após alterar o usuário ou senha do mysql para ehcp, ou se você atualizou de versões anteriores, em alguns casos)<br>
	<br><br><a href='?op=dofixapacheconfigssl'>Corrigir Configuração do Apache com SSL</a>(use com atenção,há riscos)<br><br>
	<br><a href='?op=dofixapacheconfignonssl'>Corrigir Configuração do Apache sem SSL</a><br>
	<br><a href='?op=dofixapacheconfignonssl2'>Corrigir Configuração do Apache sem SSL, alternativa 2</a> - use se a primeira alternativa não funcionou. este desabilita configurações customizadas do apache, qualquer uma<br>
	<br>
	<hr>
	<a href='?op=listservers'>Listar/Adicionar Servidores/ IP's</a><br>
	<hr>
	Regras: <a href='?op=list_roles'>List</a> - <a href='?op=assign_role'>Atribuir Regras</a><br>

	<hr>
	Experimental:
	<br><a href='?op=donewsyncdns'>Nova Sincronização de DNS - Multiservidor</a>
	<br><a href='?op=donewsyncdomains'>Nova Sincronização de Domínios - Multiservidor</a><br>
	<br><a href='?op=multiserver_add_domain'>Adicionar Domínio Multiservidor</a>
	<hr>

	"
,

'select_language'=>
"
(<a href='?op=setlanguage&id=en'>En</a>
<a href='?op=setlanguage&id=tr'>Tr</a>
<a href='?op=setlanguage&id=german'>German</a>
<a href='?op=setlanguage&id=spanish'>Spanish</a>
<a href='?op=setlanguage&id=fr'>Fr</a>
<a href='?op=setlanguage&id=lv'>Latvie&#353;u</a>
<a href='?op=setlanguage&id=it'>Italian</a>
<a href='?op=setlanguage&id=pt_BR'>Brazillian Portuguese</a>
<a href='?op=setlanguage&id=zh'>Taiwan</a>
)
<br>
<table>
<tr>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=en'><img height=30 width=50 src=images/en.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=tr'><img height=30 width=50 src=images/tr.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=german'><img height=30 width=50 src=images/german.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=spanish'><img height=30 width=50 src=images/spanish.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=fr'><img height=30 width=50 src=images/fr.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=lv'><img height=30 width=50 src=images/lv.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=pt_BR'><img height=30 width=50 src=images/pt_BR.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=it'><img height=30 width=50 src=images/it.jpg border=0></a></td>
<td style=\" padding: 3px; \"><a href='?op=setlanguage&id=zh'><img height=30 width=50 src=images/zh.jpg border=0></a></td>

</tr>
</table>
",

''=>''
);




?>

