// Cria um endpoint seguro para ler a planilha do SharePoint via PHP (Ignora CORS)
add_action('rest_api_init', function () {
    register_rest_route('sesc/v1', '/licitacoes', array(
        'methods' => 'GET',
        'callback' => 'obter_dados_planilha_sharepoint',
        'permission_callback' => '__return_true'
    ));
});

function obter_dados_planilha_sharepoint() {
    // A sua URL de download direto do SharePoint que você validou que funciona
    $url = 'https://sescpe1-my.sharepoint.com/:u:/g/personal/vssouza_sescpe_com_br/IQB98Ua-WMFVRJCAYoqmRw-QAbFZ5Sn7z-ACtYj-ii6DofE?download=1';
    
    // Faz a requisição pelo servidor PHP do Sesc
    $response = wp_remote_get($url, array('timeout' => 15));
    
    if (is_wp_error($response)) {
        return new WP_Error('erro_conexao', 'Não foi possível conectar ao SharePoint', array('status' => 500));
    }
    
    $body = wp_remote_retrieve_body($response);
    
    // Retorna o arquivo binário do Excel com o cabeçalho correto para o JavaScript ler
    return new WP_REST_Response(array(
        'base64_data' => base64_encode($body)
    ), 200);
}
