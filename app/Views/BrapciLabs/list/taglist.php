<div class="content">
    <div class="tags-container">
        <div class="tags-header">
            <h2>Tags/Termos</h2>
            <div class="download-buttons">
                <button class="btn btn-download" onclick="downloadTags('csv')" title="Download em CSV">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button class="btn btn-download" onclick="downloadTags('txt')" title="Download em TXT">
                    <i class="fas fa-file-alt"></i> TXT
                </button>
                <button class="btn btn-download" onclick="downloadTags('json')" title="Download em JSON">
                    <i class="fas fa-file-code"></i> JSON
                </button>
            </div>
        </div>

        <div class="tags-grid">
            <?php
            if (!empty($tags)) {
                $columnSize = ceil(count($tags) / 3);
                $columns = array_chunk($tags, $columnSize);
                
                foreach ($columns as $column) {
                    echo '<div class="tags-column">';
                    foreach ($column as $tag) {
                        echo '<div class="tag-item">' . htmlspecialchars($tag) . '</div>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>

        <div class="tags-footer">
            <p><strong>Total de termos:</strong> <?php echo count($tags); ?></p>
        </div>
    </div>
</div>

<style>
    .tags-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 30px;
        margin: 20px 0;
    }

    .tags-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 20px;
    }

    .tags-header h2 {
        margin: 0;
        color: #333;
        font-size: 24px;
    }

    .download-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-download {
        padding: 10px 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-download:active {
        transform: translateY(0);
    }

    .btn-download i {
        font-size: 16px;
    }

    .tags-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 30px;
    }

    .tags-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .tag-item {
        background: white;
        padding: 12px 16px;
        border-radius: 6px;
        border-left: 4px solid #667eea;
        color: #495057;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .tag-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
        border-left-color: #764ba2;
    }

    .tags-footer {
        text-align: center;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
        color: #666;
    }

    /* Responsivo para tablets */
    @media (max-width: 1024px) {
        .tags-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Responsivo para celulares */
    @media (max-width: 768px) {
        .tags-grid {
            grid-template-columns: 1fr;
        }

        .tags-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .download-buttons {
            width: 100%;
            flex-wrap: wrap;
        }

        .btn-download {
            flex: 1;
            min-width: 100px;
            justify-content: center;
        }
    }
</style>

<script>
    function downloadTags(format) {
        const tags = <?php echo json_encode($tags); ?>;

        if (!tags || tags.length === 0) {
            alert('Nenhum tag disponível para download.');
            return;
        }

        let content = '';
        let filename = 'tags';
        let mimeType = 'text/plain';

        switch (format) {
            case 'csv':
                content = tags.map(tag => `"${tag.replace(/"/g, '""')}"`).join('\n');
                filename = 'tags.csv';
                mimeType = 'text/csv;charset=utf-8;';
                break;

            case 'txt':
                content = tags.join('\n');
                filename = 'tags.txt';
                mimeType = 'text/plain;charset=utf-8;';
                break;

            case 'json':
                content = JSON.stringify(tags, null, 2);
                filename = 'tags.json';
                mimeType = 'application/json;charset=utf-8;';
                break;
        }

        const blob = new Blob([content], { type: mimeType });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>