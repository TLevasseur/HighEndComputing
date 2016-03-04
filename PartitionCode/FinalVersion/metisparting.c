#include "stdio.h"
#include "metis.h"
#include "unistd.h"

#define configfile "config.ini"
#define datafile "topart.csr"

int getconfig(int * config);
int setoptions(int * options, int * config);
int gettype();
int getGraphData();

int mode, meshmode, graphmode;
int n = 0, ncon = 0, *xadj = NULL, *adjncy = NULL, *vsize = NULL, *vwgt = NULL, *adjwgt = NULL, nparts, edgecut = 0, *partition = NULL, *epart = NULL, *npart = NULL, m = 0, ncommon = 1;
int ne, nn, *eptr, *eind; 

int main(){
	int config[9], i;
	if(getconfig(config)==0){
		printf("Config file not properly loaded\n");
		return -1;
	}
	int options[METIS_NOPTIONS];
	METIS_SetDefaultOptions(options);
	if(setoptions(options, config) == 0){
		printf("Incorrect configuration file\n");
		return -1;
	}
	if(mode==0){
		getGraphData();
		printf("\n%d %d %d\n", n, ncon, nparts);
		for(i = 0; i < n+1 ; i++) printf("%d ",xadj[i]);
		printf("\n");
		for(i = 0; i < 2*m ; i++) printf("%d ",adjncy[i]);
		printf("\n");
		partition = (int *) malloc(sizeof(int) * (n));
		switch(graphmode){
			case 0:
				printf("Recursive graph parting\n");
				METIS_PartGraphRecursive(&n, &ncon, xadj, adjncy, vwgt, vsize, 					adjwgt, &nparts, NULL, NULL, options, &edgecut,	
				partition);	
				printf("Done!\n");
			break;
			case 1:
				printf("Kway graph parting\n");
				METIS_PartGraphKway(&n, &ncon, xadj, adjncy, NULL, NULL, 					NULL, &nparts, NULL, NULL, NULL, &edgecut,	
				partition);	
				printf("Done!\n");
			break;
			default:			
				printf("The Graph partitioning mode was not appropiate\n");
				return -1;
			break;
		}
		for(i=0; i < n; i++)
			printf("%d ", partition[i]);
		
	}	
	else if(mode==1){
		switch(meshmode){
			case 0:
				METIS_PartMeshDual(&ne, &nn, eptr, eind, vwgt, vsize, &ncommon, 				&nparts, NULL, options ,&edgecut, epart, npart);
			break;
			case 1:
				METIS_PartMeshNodal(&ne, &nn, eptr, eind, vwgt, vsize, &nparts,   					NULL, options ,&edgecut, epart, npart);
			break;
			default:
				printf("The Mesh mode was not appropiate\n");
				return -1;
			break;
		}
		
	}		
	else {
		printf("There is a problem with the Format field of the config file...\n");	
		exit(0);
	}
	

	
}

int getconfig(int * config){
	FILE * fp;
	int i;
	char *temp, line[100];	
	temp = (char*) malloc(sizeof(char) * 20);
	fp = fopen("config.ini","r");
	if(fp==NULL)
		return 0;
	fgets(line, 1000, fp);
	temp = strtok(line, " ");
	for(i=0;i<9 && temp!=NULL;i++){
		config[i]=atoi(temp);
		printf(temp);
		temp = strtok(NULL, " ");		
	}
	fclose(fp);
	printf("Configuration file: ");	
	for(i=0;i<9;i++)
		printf("%d ",config[i]);
	printf("\n");
	return 1;
}

int setoptions(idx_t opc[], int * config){
	if(config[0]==1){
		switch(config[1]){
			case 0:
				opc[METIS_OPTION_PTYPE] = METIS_PTYPE_KWAY;
			break;
			case 1:
				opc[METIS_OPTION_PTYPE] = METIS_PTYPE_RB;
			break;
			default:
				printf("PTYPE value is not valid\n");
				return 0;
			break;
		}
	}
	else{
		switch(config[1]){
			case 0:
				graphmode = 0;
			break;
			case 1:
				graphmode = 1;
			break;
			default:
				printf("Graph value is not valid\n");
				return 0;
			break;
		}
		
	}
	switch(config[2]){
		case 0:
			opc[METIS_OPTION_OBJTYPE]=METIS_OBJTYPE_CUT;
		break;
		case 1:
			opc[METIS_OPTION_OBJTYPE]=METIS_OBJTYPE_VOL;
		break;
		default:
			printf("OBJTYPE value is not valid\n");
			return 0;
		break;
	}
	switch(config[3]){
		case 0:
			opc[METIS_OPTION_CTYPE]=METIS_CTYPE_RM;
		break;
		case 1:
			opc[METIS_OPTION_CTYPE]=METIS_CTYPE_SHEM;
		break;
		default:
			printf("CTYPE value is not valid\n");
			return 0;
		break;
	}
	switch(config[4]){
		case 0:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_GROW;
		break;
		case 1:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_RANDOM;
		break;
		case 2:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_EDGE;
		break;
		case 3:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_NODE;
		break;
		default:
			printf("IPTYPE value is not valid\n");
			return 0;
		break;
	}
	switch(config[5]){
		case 0:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_FM;
		break;
		case 1:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_GREEDY;
		break;
		case 2:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_SEP2SIDED;
		break;
		case 3:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_SEP1SIDED;
		break;
		default:
			printf("RTYPE value is not valid\n");
			return 0;
		break;
	}
	switch(config[6]){
		case 0:
			opc[METIS_OPTION_CONTIG]=0;
		break;
		case 1:
			opc[METIS_OPTION_CONTIG]=1;
		break;
		default:
			printf("CONTIG value is not valid\n");
			return 0;
		break;
	}
	if(config[7] == 0)
		opc[METIS_OPTION_UFACTOR] = 0;
	else if(config > 0)
 		opc[METIS_OPTION_UFACTOR] = config[7];
	else{
		printf("UFACTOR value is not valid\n");
		exit(0);
	}
	if(config[8] > 0)
		nparts=config[8];
	else {
		printf("Input error, incorrect nparts");
		return 0;
	}
	switch(config[0]){
		case 0:
			mode = 0;
		break;
		case 1:
			mode = 1;
			switch(config[8]){
				case 0: 
					meshmode = 0;
				break;
				case 1:
					meshmode = 1;
				break;
				default:
					printf("MESH MODE value is not valid\n");
					return 0;
				break;
			}
			
		break;
		default:
			printf("MODE value is not valid\n");
			return 0;
		break;
	}
	return 1;
}

int getGraphData(){
	printf("Here");
	int fmt = 0, check, disp = 0, j, k, l, maxtam=1, vflag=0;
	int *temp1, *temp2, *temp3;
	char line[100], * temp;
	FILE * fp;
	int i;
	fp = fopen(DATAFILE, "r");
	if (fp == NULL) {
		printf("File not found\n");
		return -1;
	}
	//Reading header line
	fgets(line, 1000, fp);
	check = sscanf(line, "%d %d %d %d", &n, &m, &fmt, &ncon);
	if (ncon == 0) ncon = 1;
	printf("%d %d %d %d\n", n, m, fmt, ncon);
	xadj = (int * ) malloc(sizeof(int) * (m + 1));
	adjncy = (int * ) malloc(sizeof(int) * 2 * n);
	xadj[0] = 0;
        if(n < nparts){
		printf("Input error: There are less elements than partitions desired\n");
		return 0;
	}
	switch (check)
	{
	case 2: 
		for (i = 0; i < n; i++)
		{
			fgets(line, 100000, fp);
			if (line != NULL) {
				temp = strtok(line, " ");
				while (temp != NULL)
				{
					adjncy[disp] = atoi(temp);
					disp++;
					temp = strtok(NULL, " ");
				}
				xadj[i + 1] = disp;
			}

			else { 
				printf("An error ocurred");
				return 0; 
			}

		}
		break;

	case 4: 
	case 3:
		if (fmt == 0) {
			maxtam = 1;
		}
		else if (fmt == 1) {
			maxtam = 2;
			adjwgt = (int *) malloc(sizeof(int) * 2 * m);
			vsize = NULL;
			vwgt = NULL;
			temp1 = adjwgt;
		}
		else if (fmt == 10) {
			maxtam = 1;
			vwgt = (int *) malloc(sizeof(int) * n * ncon);
			vsize = NULL;
			adjwgt = NULL;
			vflag = 1;

		}
		else if (fmt == 100) {
			maxtam = 2;
			vsize = (int *)malloc(sizeof(int) * 2 * m);
			vwgt = NULL;
			adjwgt = NULL;
			temp1 = vsize;
		}
		else if (fmt == 11) {
			maxtam = 2;
			vwgt = (int *) malloc(sizeof(int) * n * ncon);
			adjwgt = (int *) malloc(sizeof(int) * 2 * m);
			vsize = NULL;
			temp1 = adjwgt;
			vflag = 1;

		}
		else if (fmt == 101) {
			maxtam = 3;
			vsize = (int *)malloc(sizeof(int) * 2 * m);
			adjwgt = (int *)malloc(sizeof(int) * 2 * m); 
			vwgt = NULL;
			temp1 = adjwgt;
			temp2 = vsize;

		}
		else if (fmt == 110) {
			maxtam = 2;
			vwgt = (int *) malloc(sizeof(int) * n * ncon);
			vsize = (int *)malloc(sizeof(int) * 2 * m);
			adjwgt = NULL;
			vflag = 1;
			temp1 = vsize;
		}
		else if (fmt == 111) {
			maxtam = 3;
			vwgt = (int *) malloc(sizeof(int) * n * ncon);
			vsize = (int *)malloc(sizeof(int) * 2 * m);
			adjwgt = (int *)malloc(sizeof(int) * 2 * m);
			vflag = 1;
			temp1 = adjwgt;
			temp2 = vsize;
		}
		for (i = 0, j = 0; i < m; i++)
		{
			fgets(line, 100000, fp);
			if (line != NULL) {
				temp = strtok(line, " ");
				if (vflag == 1) {
					for ( l = 0; l < ncon; l++)
					{
						vwgt[ncon * i + l] = atoi(temp);
						temp = strtok(NULL, " ");
					}
				}
				while (temp != NULL)
				{
					k = j % maxtam;
					switch (k) {
					case 0: 
						adjncy[disp] = atoi(temp);
						disp++;
						break;
					case 1: 
						temp1[disp-1] = atoi(temp);
						break;
					case 2:
						temp2[disp-1] = atoi(temp);
						break;
					}
					temp = strtok(NULL, " ");
					j++;
				}
				xadj[i + 1] = disp;
			}

			else {
				printf("An error ocurred");
				return 0;
			}

		}

		break;
	default:
		printf("\nIncorrect file format\n");
		break;
	}
	fclose(fp);
	return 1;

}

int getMeshData(){
	return -1;
}

