FROM varnish:6.5

RUN apt-get update && apt-get install -y git make automake libtool python-sphinx varnish-dev
# download the top of the varnish-modules 6.5 branch
RUN git clone --branch 6.5 --single-branch https://github.com/varnish/varnish-modules.git
# jump into the directory
# WORKDIR /varnish-modules
# prepare the build, build, check and install
RUN cd varnish-modules && ./bootstrap && \
    ./configure && \
    make && \
    make check -j 4 && \
    make install

COPY . /etc/varnish/