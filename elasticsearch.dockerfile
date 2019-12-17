FROM elasticsearch:5.1.1

COPY ./elasticsearch.zip /data/

RUN elasticsearch-plugin install file:/data/elasticsearch.zip
