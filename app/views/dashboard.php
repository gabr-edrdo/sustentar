<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SustentAr - Monitoramento da Qualidade do Ar</title>
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <!-- Fonte Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f5f5;
    }
    .header-title {
      color: #1a8b44;
      font-weight: bold;
      font-size: 2rem;
    }
    .section-title {
      font-size: 1.25rem;
      font-weight: bold;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .emoji {
      font-size: 3rem;
    }
    .download-section {
      background-color: black;
      color: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .download-section select,
    .download-section input {
      background-color: #808080;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px;
    }
    .download-section button {
      border-radius: 8px;
      padding: 10px 20px;
    }
    #map {
      height: 400px;
      border-radius: 15px;
      margin-top: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .error-message {
      color: red;
      font-size: 0.9rem;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="container py-4">
  <!-- Header -->
  <div class="text-start mb-4">
    <h1 class="header-title">SustentAr</h1>
    <h2>Monitoramento da qualidade do ar</h2>
    <p>
      O projeto "SustentAr" visa implementar um sistema web integrado a sensores Arduino para monitorar em tempo real
      a qualidade do ar e a temperatura em São Lourenço do Oeste. Com foco na redução da poluição atmosférica, o projeto
      busca subsidiar políticas públicas e aumentar a conscientização ambiental.
      <br>
      DADOS DEMONSTRATIVOS, NÃO CONSIDERAR!
    </p>
  </div>

  <!-- Última leitura -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-center p-3">
        <h5 class="section-title">Última leitura</h5>
        <p id="last-reading-time">Carregando...</p>
        <div class="emoji" id="air-quality-emoji">🤔</div>
        <h5 id="air-quality-status">Carregando...</h5>
        <p id="temperature">Temperatura: --°C</p>
        <p id="humidity">Umidade: --%</p>
        <p id="co">CO: -- ppm</p>
        <p id="pm25">Partículas 2.5 nm: --</p>
        <p id="pm1">Partículas 1 nm: --</p>
      </div>
    </div>

    <!-- Download CSV -->
    <div class="col-md-8">
      <div class="download-section">
        <h5>Faça download do seu relatório</h5>
        <form id="report-form">
          <div class="row mb-3">
            <div class="col-md-4">
              <select id="station" class="form-control" requires>
                <option value="1" selected>Estação 1</option>
              </select>
            </div>
            <div class="col-md-4">
              <input type="date" id="start-date" class="form-control" required>
            </div>
            <div class="col-md-4">
              <input type="date" id="end-date" class="form-control" reuired>
            </div>
          </div>
          <button type="button" id="clear-filters" class="btn btn-light">Limpar</button>
          <button type="button" id="download-csv" class="btn btn-success">Download CSV</button>
          <div id="error-message" class="error-message d-none"></div>
        </form>
      </div>
    </div>
  </div>

  <!-- Mapa -->
  <div class="row">
    <div class="col-md-12">
      <div id="map"></div>
    </div>
  </div>

  <!-- Botões -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="d-flex justify-content-center gap-3">
        <a href="https://github.com/gabr-edrdo/sustentar" class="btn btn-dark">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github me-2" viewBox="0 0 16 16">
            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
          </svg>
          Contribua
        </a>
        <a href="mailto:gabriel@sustentar.app.br" class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-data me-2" viewBox="0 0 16 16">
            <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0V9z"/>
            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
          </svg>
          Faça Parte
        </a>
        <a href="#" class="btn btn-info text-white" id="methodology-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book me-2" viewBox="0 0 16 16">
            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
          </svg>
          Metodologia
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    // Função para calcular PPM de CO baseado no ratio
    function calculateCOPPM(ratio) {
      if (ratio >= 1.23) {
        // De 200 a 500 ppm: y = -0.0021x + 2.12
        return Math.round((2.12 - ratio) / 0.0021);
      } else if (ratio >= 0.87) {
        // De 500 a 800 ppm: y = -0.000667x + 1.403333
        return Math.round((1.403333 - ratio) / 0.000667);
      } else if (ratio >= 0.78) {
        // De 800 a 1000 ppm: y = -0.00045x + 1.23
        return Math.round((1.23 - ratio) / 0.00045);
      } else {
        // Acima de 1000 ppm
        return Math.round(1000 + (0.78 - ratio) / 0.00045);
      }
    }

    // Função para buscar e atualizar a última leitura
    function fetchLatestReading() {
      $.ajax({
        url: 'https://sustentar.app.br/api/leituras/latest',
        method: 'GET',
        success: function(response) {
          if (response && response.length > 0) {
            const reading = response[0];
            const dados = JSON.parse(reading.dados_json);
            const timestamp = new Date(reading.created_at).toLocaleString('pt-BR');

            // Calcular PPM de CO usando o ratio
            const co = calculateCOPPM(dados.mq9_ratio);

            $('#last-reading-time').text(timestamp);
            $('#temperature').text(`Temperatura: ${dados.temperature ?? '--'}°C`);
            $('#humidity').text(`Umidade: ${dados.humidity ?? '--'}%`);
            $('#co').text(`CO: ${co} ppm`);
            $('#pm25').text(`Partículas 2.5 nm: ${dados.pm25}`);
            $('#pm1').text(`Partículas 1 nm: ${dados.pm1}`);

            // Atualizar qualidade do ar
            const airQuality = classifyAirQuality(co, dados.pm25, dados.pm1);
            $('#air-quality-emoji').text(airQuality.emoji);
            $('#air-quality-status').text(airQuality.status);
          }
        },
        error: function() {
          console.error('Erro ao buscar dados mais recentes');
        }
      });
    }

    // Buscar dados a cada 30 segundos
    fetchLatestReading();
    setInterval(fetchLatestReading, 30000);

    // Função para classificar qualidade do ar
    function classifyAirQuality(co, pm25, pm1) {
      const coIndex = co <= 50 ? 1 : co <= 100 ? 2 : co <= 150 ? 3 : co <= 200 ? 4 : 5;
      const pm25Index = pm25 <= 30 ? 1 : pm25 <= 60 ? 2 : pm25 <= 90 ? 3 : pm25 <= 120 ? 4 : 5;
      const pm1Index = pm1 <= 20 ? 1 : pm1 <= 40 ? 2 : pm1 <= 60 ? 3 : pm1 <= 80 ? 4 : 5;

      const maxIndex = Math.max(coIndex, pm25Index, pm1Index);

      switch (maxIndex) {
        case 1: return { emoji: "😀", status: "Boa" };
        case 2: return { emoji: "😐", status: "Moderada" };
        case 3: return { emoji: "😷", status: "Ruim" };
        case 4: return { emoji: "😡", status: "Muito Ruim" };
        case 5: return { emoji: "☠️", status: "Perigosa" };
      }
    }

    // Inicializar o mapa
    const map = L.map('map').setView([-26.3557, -52.8498], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([-26.3557, -52.8498]).addTo(map);
    marker.bindPopup("<b>Estação 01</b><br>São Lourenço do Oeste").openPopup();

    // Função para limpar filtros
    $('#clear-filters').click(function () {
      $('#station').val('');
      $('#start-date').val('');
      $('#end-date').val('');
      $('#error-message').addClass('d-none');
    });

    // Função para o download do CSV
    $('#download-csv').click(function () {
      const station = $('#station').val();
      const startDate = $('#start-date').val();
      const endDate = $('#end-date').val();

      if (!station || !startDate || !endDate) {
        $('#error-message').text('Por favor, preencha todos os campos.').removeClass('d-none');
        return;
      }

      $.ajax({
        url: 'https://sustentar.app.br/api/leituras',
        method: 'GET',
        data: {
          start_date: startDate,
          end_date: endDate
        },
        success: function(response) {
          if (!response || response.length === 0 || response.message === "Nenhuma leitura encontrada.") {
            $('#error-message')
              .text('Não existem dados para o período selecionado.')
              .removeClass('d-none');
            return;
          }

          // Converter dados para CSV
          let csv = 'Data,Temperatura,Umidade,CO (ppm),PM2.5,PM1\n';
          
          response.forEach(reading => {
            try {
              const dados = JSON.parse(reading.dados_json);
              const timestamp = new Date(reading.created_at).toLocaleString('pt-BR');
              const co = calculateCOPPM(dados.mq9_ratio);
              
              csv += `${timestamp},${dados.temperature !== null ? dados.temperature : ''},${dados.humidity !== null ? dados.humidity : ''},${co},${dados.pm25},${dados.pm1}\n`;
            } catch (e) {
              console.error('Erro ao processar leitura:', e);
            }
          });

          // Download do arquivo
          const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = `relatorio_${startDate}_${endDate}.csv`;
          link.click();
          
          $('#error-message').addClass('d-none');
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Erro ao gerar o relatório. ';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage += xhr.responseJSON.message;
          } else {
            errorMessage += 'Por favor, tente novamente mais tarde.';
          }
          $('#error-message')
            .text(errorMessage)
            .removeClass('d-none');
        }
      });
    });

    $('#methodology-link').click(function(e) {
      e.preventDefault();
      alert('Link da metodologia ainda não definido');
    });
  });
</script>

</body>
</html>